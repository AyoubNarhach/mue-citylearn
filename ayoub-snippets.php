<?php
/**
 * Plugin Name: Ayoub Snippets (MU)
 * Description: Tous les snippets migrés depuis Code Snippets.
 */

// === Colle ici tous tes snippets, les uns à la suite des autres ===

/**
 * Rafraîchissement auto après la première connexion
 * — évite le 404 du premier hit en forçant un reload propre une seule fois.
 */
add_action('wp_login', function($user_login, $user){
  // Dépose un cookie de courte durée pour signaler "rafraîchir une fois"
  $secure = is_ssl();
  $domain = defined('COOKIE_DOMAIN') && COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
  $path   = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
  setcookie('ldua_refresh_once', '1', time() + 120, $path, $domain, $secure, true);
}, 10, 2);

add_action('template_redirect', function(){
  // Front uniquement (pas admin, pas AJAX/REST)
  if (is_admin() || (function_exists('wp_doing_ajax') && wp_doing_ajax()) || (defined('REST_REQUEST') && REST_REQUEST)) {
    return;
  }

  // Si pas notre cookie => rien à faire
  if (empty($_COOKIE['ldua_refresh_once'])) return;

  // Pas de boucle : si déjà rafraîchi, on nettoie le cookie et on sort
  if (isset($_GET['ldua_refreshed'])) {
    $secure = is_ssl();
    $domain = defined('COOKIE_DOMAIN') && COOKIE_DOMAIN ? COOKIE_DOMAIN : '';
    $path   = defined('COOKIEPATH') && COOKIEPATH ? COOKIEPATH : '/';
    setcookie('ldua_refresh_once', '', time() - 3600, $path, $domain, $secure, true);
    return;
  }

  // On neutralise le cache pour CE hit (utile avec LiteSpeed/varnish)
  if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
  if (!defined('LSCACHE_NO_CACHE')) define('LSCACHE_NO_CACHE', true);
  if (function_exists('nocache_headers')) nocache_headers();
  do_action('litespeed_control_set_nocache', 'ldua refresh-on-first-login');

  // Redirige (302) vers la même URL avec un flag => effet "actualiser" une seule fois
  $current = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
  $current = remove_query_arg('ldua_refreshed', $current);
  $target  = add_query_arg('ldua_refreshed', '1', $current);
  if (0 === strpos($target, '/')) {
    $target = home_url($target);
  }

  wp_safe_redirect($target, 302);
  exit;
}, 0);


// === Utilisateurs ===
/**
 * [ld_user_admin] — front admin utilisateurs (administrator + lms_admin)
 * V7.1 — UI table sans colonnes dates + Email 1 ligne + bulk actions (Supprimer/Activer/Suspendre)
 *        Statuts compte (créé / activé / suspendu) + popup profil + CSV dates + "Manager"
 *        Emails WP en FR (switch locale), 1er hit frais (anti-cache), purge LiteSpeed, exclusion admins
 */

if (!defined('LDUA_BOOTSTRAP')) {
  define('LDUA_BOOTSTRAP', 'v7.1');

  /* =========================================================
   *  Rôle lms_admin : caps nécessaires
   * ========================================================= */
  add_action('init', function () {
    if (!get_role('lms_admin')) {
      add_role('lms_admin', 'Administrateur LMS', ['read' => true]);
    }
    if ($role = get_role('lms_admin')) {
      foreach ([
        'read', 'list_users', 'edit_users', 'create_users', 'delete_users',
        'promote_users', 'remove_users', 'add_users', 'manage_options',
        'upload_files'
      ] as $cap) {
        if (!$role->has_cap($cap)) $role->add_cap($cap);
      }
    }
  }, 1);

  /* ➜ Autoriser lms_admin à supprimer tout non-admin (méta-cap delete_user) */
  add_filter('map_meta_cap', function ($caps, $cap, $user_id, $args) {
    if ($cap !== 'delete_user') return $caps;

    $target_id = isset($args[0]) ? (int)$args[0] : 0;
    if (!$target_id) return $caps;

    $actor  = get_userdata($user_id);
    $target = get_userdata($target_id);
    if (!$actor || !$target) return $caps;

    $actor_is_lms  = in_array('lms_admin', (array)$actor->roles, true);
    $target_is_adm = in_array('administrator', (array)$target->roles, true);

    if ($actor_is_lms && !$target_is_adm) {
      return ['delete_users'];
    }
    return $caps;
  }, 10, 4);

  /* =========================================================
   *  Utils
   * ========================================================= */
  if (!function_exists('ldua_can_manage')) {
    function ldua_can_manage()
    {
      if (!is_user_logged_in()) return false;
      $u = wp_get_current_user();
      return (bool)array_intersect(['administrator', 'lms_admin'], (array)$u->roles);
    }
  }

  if (!function_exists('ldua_safe_redirect')) {
    function ldua_safe_redirect($url, $extra = [])
    {
      if (!$url) {
        $url = wp_get_referer() ?: (get_permalink() ?: home_url('/'));
      }
      if (!empty($extra)) $url = add_query_arg($extra, $url);
      wp_safe_redirect($url);
      exit;
    }
  }

  if (!function_exists('ldua_current_page_url')) {
    function ldua_current_page_url()
    {
      $base = get_permalink();
      if (!$base) $base = home_url(add_query_arg([], $_SERVER['REQUEST_URI']));
      $keep = ['ldua_group', 'ldua_course', 'ldua_s', 'ldua_paged', 'ldua_msg', 'ldua_err', 'ldua_inactive', 'ldua_start', 'ldua_end', 'ldua_per_page', 'ldua_status'];
      $qs = [];
      foreach ($keep as $k) {
        if (isset($_GET[$k]) && !is_array($_GET[$k])) $qs[$k] = sanitize_text_field($_GET[$k]);
      }
      return add_query_arg($qs, $base);
    }
  }

  if (!function_exists('ldua_norm')) {
    function ldua_norm($s)
    {
      $s = trim((string)$s);
      $s = function_exists('remove_accents') ? remove_accents($s) : @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $s);
      $s = strtolower($s);
      $s = preg_replace('~\s+~', ' ', $s);
      return $s;
    }
  }

  if (!function_exists('ldua_get_user_ids_for_course')) {
    function ldua_get_user_ids_for_course($course_id)
    {
      $course_id = (int)$course_id;
      if (!$course_id) return [];
      if (function_exists('learndash_get_users_for_course')) {
        $uq = learndash_get_users_for_course($course_id, ['number' => -1, 'fields' => 'ID']);
        if ($uq instanceof WP_User_Query) return array_map('intval', (array)$uq->get_results());
        if (is_array($uq)) return array_map('intval', $uq);
      }
      if (function_exists('ld_course_access_list')) {
        $res = ld_course_access_list($course_id);
        if (is_string($res)) $res = array_filter(array_map('intval', explode(',', $res)));
        if (is_array($res)) return array_map('intval', $res);
      }
      return [];
    }
  }

  if (!function_exists('ldua_get_group_course_ids')) {
    function ldua_get_group_course_ids($group_id)
    {
      $ids = [];
      if (function_exists('learndash_group_enrolled_courses')) {
        foreach ((array)learndash_group_enrolled_courses($group_id) as $cid) {
          $cid = (int)$cid;
          if ($cid) $ids[] = $cid;
        }
      }
      if (empty($ids)) {
        $m = get_post_meta($group_id, 'learndash_group_enrolled_courses', true);
        if (is_array($m)) {
          foreach ($m as $cid) {
            $cid = (int)$cid;
            if ($cid) $ids[] = $cid;
          }
        }
      }
      return array_values(array_unique($ids));
    }
  }

  /* =========================================================
   *  Statut compte: créé / activé / suspendu (via dates)
   * ========================================================= */
  if (!defined('LDUA_META_ACTIVATION')) define('LDUA_META_ACTIVATION', 'ldua_activation_date'); // Y-m-d
  if (!defined('LDUA_META_SUSPENSION')) define('LDUA_META_SUSPENSION', 'ldua_suspension_date'); // Y-m-d

  if (!function_exists('ldua_normalize_date_input')) {
    function ldua_normalize_date_input($s)
    {
      $s = trim((string)$s);
      if ($s === '') return '';
      if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return $s;

      if (preg_match('/^(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})$/', $s, $m)) {
        $d  = str_pad($m[1], 2, '0', STR_PAD_LEFT);
        $mo = str_pad($m[2], 2, '0', STR_PAD_LEFT);
        $y  = $m[3];
        return $y . '-' . $mo . '-' . $d;
      }
      if (preg_match('/^(\d{4})[\/\-](\d{1,2})[\/\-](\d{1,2})$/', $s, $m)) {
        $y  = $m[1];
        $mo = str_pad($m[2], 2, '0', STR_PAD_LEFT);
        $d  = str_pad($m[3], 2, '0', STR_PAD_LEFT);
        return $y . '-' . $mo . '-' . $d;
      }
      return '';
    }
  }

  if (!function_exists('ldua_parse_ymd_to_ts')) {
    function ldua_parse_ymd_to_ts($ymd, $end_of_day = false)
    {
      $ymd = trim((string)$ymd);
      if ($ymd === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd)) return 0;
      try {
        $tz   = function_exists('wp_timezone') ? wp_timezone() : new DateTimeZone('UTC');
        $time = $end_of_day ? '23:59:59' : '00:00:00';
        $dt   = new DateTimeImmutable($ymd . ' ' . $time, $tz);
        return $dt->getTimestamp();
      } catch (Exception $e) {
        return 0;
      }
    }
  }

  if (!function_exists('ldua_user_is_exempt_from_dates')) {
    function ldua_user_is_exempt_from_dates($user_id)
    {
      $u = get_userdata((int)$user_id);
      if (!$u) return true;
      $roles = (array)$u->roles;
      return in_array('administrator', $roles, true) || in_array('lms_admin', $roles, true);
    }
  }

  if (!function_exists('ldua_get_account_state')) {
    function ldua_get_account_state($user_id)
    {
      $act = (string)get_user_meta((int)$user_id, LDUA_META_ACTIVATION, true);
      $sus = (string)get_user_meta((int)$user_id, LDUA_META_SUSPENSION, true);

      $act = ldua_normalize_date_input($act);
      $sus = ldua_normalize_date_input($sus);

      $now = current_time('timestamp');
      $act_ts = $act ? ldua_parse_ymd_to_ts($act, false) : 0;
      $sus_ts = $sus ? ldua_parse_ymd_to_ts($sus, false) : 0;

      if ($sus_ts && $now >= $sus_ts) {
        return ['state' => 'suspended', 'activation' => $act, 'suspension' => $sus];
      }
      if ($act_ts && $now >= $act_ts) {
        return ['state' => 'active', 'activation' => $act, 'suspension' => $sus];
      }
      return ['state' => 'created', 'activation' => $act, 'suspension' => $sus];
    }
  }

  if (!function_exists('ldua_user_can_access_platform')) {
    function ldua_user_can_access_platform($user_id)
    {
      if (ldua_user_is_exempt_from_dates($user_id)) {
        return ['allow' => true, 'code' => 'ok', 'message' => '', 'state' => 'exempt'];
      }

      $st = ldua_get_account_state($user_id);

      if ($st['state'] === 'suspended') {
        $d = $st['suspension'] ?: '';
        return [
          'allow' => false,
          'code' => 'suspended',
          'state' => 'suspended',
          'message' => $d ? ('Votre compte est suspendu depuis le ' . $d . '.') : 'Votre compte est suspendu.'
        ];
      }

      if ($st['state'] === 'created') {
        if (!empty($st['activation'])) {
          return [
            'allow' => false,
            'code' => 'not_active',
            'state' => 'created',
            'message' => 'Votre compte n’est pas encore activé (activation prévue le ' . $st['activation'] . ').'
          ];
        }
        return [
          'allow' => false,
          'code' => 'not_active',
          'state' => 'created',
          'message' => 'Votre compte a été créé mais n’est pas activé. Merci de contacter votre administrateur.'
        ];
      }

      return ['allow' => true, 'code' => 'ok', 'message' => '', 'state' => 'active'];
    }
  }

  /* --- Message sur wp-login --- */
  add_filter('login_message', function ($message) {
    if (!empty($_GET['ldua_blocked'])) {
      $code = sanitize_text_field($_GET['ldua_blocked']);
      $txt = ($code === 'suspended')
        ? '⛔ Votre compte est suspendu.'
        : '⛔ Votre compte n’est pas activé.';
      $message .= '<div style="border-left:4px solid #b91c1c;background:#fff5f5;padding:10px 12px;margin:10px 0;color:#7f1d1d;">' . $txt . '</div>';
    }
    return $message;
  });

  /* --- Bloquer connexion WordPress si non activé / suspendu --- */
  add_filter('wp_authenticate_user', function ($user) {
    if (!$user || is_wp_error($user) || empty($user->ID)) return $user;
    if (ldua_user_is_exempt_from_dates($user->ID)) return $user;

    $chk = ldua_user_can_access_platform($user->ID);
    if (!empty($chk['allow'])) return $user;

    return new WP_Error('ldua_blocked', $chk['message']);
  }, 20, 1);

  /* --- Forcer logout si statut devient bloquant (front + wp-admin) --- */
  $ldua_force_logout_if_blocked = function () {
    if (!is_user_logged_in()) return;
    $uid = get_current_user_id();
    if (ldua_user_is_exempt_from_dates($uid)) return;

    $chk = ldua_user_can_access_platform($uid);
    if (!empty($chk['allow'])) return;

    if (function_exists('wp_destroy_all_sessions')) {
      wp_destroy_all_sessions($uid);
    }
    wp_logout();

    $url = add_query_arg(['ldua_blocked' => $chk['code']], wp_login_url());
    wp_safe_redirect($url);
    exit;
  };
  add_action('template_redirect', $ldua_force_logout_if_blocked, 1);
  add_action('admin_init', $ldua_force_logout_if_blocked, 1);

  /* =========================================================
   *  Affectations (apprenants)
   * ========================================================= */
  if (!function_exists('ldua_enroll_user_to_groups')) {
    function ldua_enroll_user_to_groups($user_id, array $group_ids)
    {
      $group_ids = array_values(array_unique(array_filter(array_map('intval', $group_ids))));
      if (empty($group_ids)) return;

      if (function_exists('ld_update_group_access')) {
        foreach ($group_ids as $gid) ld_update_group_access($user_id, $gid, false);
      }

      if (function_exists('learndash_get_users_group_ids') && function_exists('learndash_set_users_group_ids')) {
        $current = (array)learndash_get_users_group_ids($user_id);
        $target = array_values(array_unique(array_merge(array_map('intval', $current), $group_ids)));
        sort($current);
        sort($target);
        if ($current !== $target) learndash_set_users_group_ids($user_id, $target, true);
      }

      if (function_exists('learndash_get_users_group_ids')) {
        $have = (array)learndash_get_users_group_ids($user_id);
        foreach ($group_ids as $gid) {
          if (!in_array($gid, $have, true)) {
            if (function_exists('ulgm_add_user_to_group')) @ulgm_add_user_to_group($user_id, $gid);
            if (function_exists('ulgm') && method_exists(ulgm()->group_management ?? null, 'add_user_to_group')) {
              @ulgm()->group_management->add_user_to_group($user_id, $gid);
            }
            if (function_exists('ld_update_group_access')) ld_update_group_access($user_id, $gid, false);
          }
        }
      }
    }
  }

  if (!function_exists('ldua_enroll_user_to_courses')) {
    function ldua_enroll_user_to_courses($user_id, array $course_ids)
    {
      $course_ids = array_values(array_unique(array_filter(array_map('intval', $course_ids))));
      if (empty($course_ids)) return;
      foreach ($course_ids as $cid) {
        if (function_exists('ld_update_course_access')) ld_update_course_access($user_id, $cid, false);
        if (function_exists('learndash_user_clear_course_progress_cache')) learndash_user_clear_course_progress_cache($user_id, $cid);
      }
    }
  }

  /* =========================================================
   *  Affectations (leaders / Managers)
   * ========================================================= */
  if (!function_exists('ldua_assign_group_leader_to_groups')) {
    function ldua_assign_group_leader_to_groups($user_id, array $group_ids)
    {
      $group_ids = array_values(array_unique(array_filter(array_map('intval', $group_ids))));
      if (empty($group_ids)) return;

      foreach ($group_ids as $gid) {
        if (function_exists('ld_update_leader_group_access')) {
          @ld_update_leader_group_access($user_id, $gid, false);
          continue;
        }

        if (function_exists('learndash_get_groups_administrators') && function_exists('learndash_set_groups_administrators')) {
          $current = (array)learndash_get_groups_administrators($gid);
          $current = array_map('intval', $current);
          if (!in_array((int)$user_id, $current, true)) {
            $current[] = (int)$user_id;
            $current = array_values(array_unique($current));
            @learndash_set_groups_administrators($gid, $current);
          }
          continue;
        }

        if (function_exists('ulgm') && method_exists(ulgm()->group_management ?? null, 'add_leader_to_group')) {
          @ulgm()->group_management->add_leader_to_group($user_id, $gid);
          continue;
        }

        $key = 'ld_group_leaders';
        $leaders = get_post_meta($gid, $key, true);
        if (!is_array($leaders)) $leaders = array_filter(array_map('intval', (array)$leaders));
        if (!in_array((int)$user_id, $leaders, true)) {
          $leaders[] = (int)$user_id;
          update_post_meta($gid, $key, array_values(array_unique($leaders)));
        }
      }
    }
  }

  /* =========================================================
   *  Emails FR + helper
   * ========================================================= */
  if (!function_exists('ldua_send_mail_fr')) {
    function ldua_send_mail_fr($to, $subject, $message, $headers = [])
    {
      $restore = null;
      if (function_exists('switch_to_locale')) {
        $restore = determine_locale();
        switch_to_locale('fr_FR');
      }
      add_filter('wp_mail_content_type', function () {
        return 'text/html; charset=UTF-8';
      });
      $ok = wp_mail($to, $subject, $message, $headers);
      remove_filter('wp_mail_content_type', '__return_false', 10);
      if (function_exists('restore_previous_locale')) {
        restore_previous_locale();
      } elseif ($restore && function_exists('switch_to_locale')) {
        switch_to_locale($restore);
      }
      return $ok;
    }
  }

  /* =========================================================
   *  Correctifs 1ère connexion (anti-404 / anti-cache)
   * ========================================================= */
  if (!function_exists('ldua_mark_first_login_fresh')) {
    function ldua_mark_first_login_fresh($uid)
    {
      update_user_meta($uid, 'ldua_first_login_fresh', 1);
    }
  }

  add_filter('login_redirect', function ($redirect_to, $requested, $user) {
    if (is_wp_error($user) || empty($user->ID)) return $redirect_to;

    if (get_user_meta($user->ID, 'ldua_first_login_fresh', true)) {
      delete_user_meta($user->ID, 'ldua_first_login_fresh');

      clean_user_cache($user->ID);
      wp_cache_delete($user->ID, 'users');
      wp_cache_delete($user->ID, 'user_meta');

      $home = home_url('/');
      do_action('litespeed_purge_url', $home);

      $user_obj = new WP_User($user->ID);
      $roles = $user_obj->roles;
      $primary_role = !empty($roles) ? $roles[0] : 'subscriber';

      $target_url = '';
      switch ($primary_role) {
        case 'administrator':
        case 'lms_admin':
          $target_url = admin_url();
          break;

        case 'group_leader':
          if (function_exists('learndash_get_page_id')) {
            $groups_page_id = learndash_get_page_id('groups');
            if ($groups_page_id) $target_url = get_permalink($groups_page_id);
            if (!$target_url) {
              $profile_page_id = learndash_get_page_id('profile');
              if ($profile_page_id) $target_url = get_permalink($profile_page_id);
            }
          }
          break;

        case 'subscriber':
        default:
          if (function_exists('learndash_get_page_id')) {
            $courses_page_id = learndash_get_page_id('courses');
            if ($courses_page_id) $target_url = get_permalink($courses_page_id);
            if (!$target_url) {
              $profile_page_id = learndash_get_page_id('profile');
              if ($profile_page_id) $target_url = get_permalink($profile_page_id);
            }
          }
          break;
      }

      if (!$target_url) {
        $safe_pages = ['bienvenue', 'welcome', 'a-propos', 'about', 'contact'];
        foreach ($safe_pages as $slug) {
          $page = get_page_by_path($slug);
          if ($page) {
            $target_url = get_permalink($page->ID);
            break;
          }
        }
      }
      if (!$target_url) $target_url = $home;

      return add_query_arg(['ldua_nocache' => '1', '_t' => time()], $target_url);
    }
    return $redirect_to;
  }, 10, 3);

  add_action('template_redirect', function () {
    if (!empty($_GET['ldua_nocache'])) {
      if (function_exists('nocache_headers')) nocache_headers();
      header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
      header('Pragma: no-cache');
      do_action('litespeed_control_set_nocache', 'ldua first login');
    }
  }, 1);

  add_action('wp_login', function ($user_login, $user) {
    $home = home_url('/');
    do_action('litespeed_purge_url', $home);
  }, 10, 2);

  /* =========================================================
   *  Helpers state label/color
   * ========================================================= */
  if (!function_exists('ldua_state_label_color')) {
    function ldua_state_label_color($state)
    {
      if ($state === 'active') return ['label' => 'Activé', 'color' => '#16a34a'];
      if ($state === 'suspended') return ['label' => 'Suspendu', 'color' => '#b91c1c'];
      return ['label' => 'Créé', 'color' => '#b45309'];
    }
  }

  /* =========================================================
   *  AJAX: template CSV
   * ========================================================= */
  add_action('wp_ajax_ldua_template_download', function () {
    $nonce = $_GET['ldua_template_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_template_download')) wp_die('Nonce invalide.');

    if (!ldua_can_manage()) wp_die('Accès refusé.');

    $filename = 'ldua_modele_import.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    // BOM UTF-8 (Excel)
    echo "\xEF\xBB\xBF";
    echo "email;prenom;nom;identifiant;mot_de_passe;role;groupes;parcours;date_debut;date_suspension\n";
    echo "exemple@domaine.com;Ali;Test;ali.test;MotDePasse123;subscriber;123|Groupe A;456|Parcours B;2026-01-15;\n";
    exit;
  });

  /* =========================================================
   *  AJAX: create user
   * ========================================================= */
  add_action('wp_ajax_ldua_create_user', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);

    $nonce = $_POST['ldua_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_create_user_ajax')) {
      wp_send_json_error(['message' => 'Nonce invalide.']);
    }

    $email = sanitize_email($_POST['email'] ?? '');
    if (!$email || !is_email($email)) wp_send_json_error(['message' => 'Email invalide.']);

    $first = sanitize_text_field($_POST['first_name'] ?? '');
    $last  = sanitize_text_field($_POST['last_name'] ?? '');
    $role  = sanitize_text_field($_POST['role'] ?? 'subscriber');
    $allowed_roles = ['subscriber', 'group_leader', 'lms_admin'];
    if (!in_array($role, $allowed_roles, true)) $role = 'subscriber';

    // username
    $username = sanitize_user($_POST['username'] ?? '', true);
    if (!$username) {
      $base = sanitize_user(str_replace(['@', '.'], ['_', '_'], $email), true);
      $username = $base;
      if (username_exists($username)) {
        $username = $base . '_' . wp_generate_password(4, false, false);
      }
    } else {
      if (username_exists($username)) {
        wp_send_json_error(['message' => 'Identifiant déjà utilisé.']);
      }
    }

    // password
    $password = (string)($_POST['password'] ?? '');
    $password = trim($password);
    if (!$password) $password = wp_generate_password(12, true, false);

    if (email_exists($email)) {
      wp_send_json_error(['message' => 'Cet email existe déjà.']);
    }

    $user_id = wp_insert_user([
      'user_login'   => $username,
      'user_pass'    => $password,
      'user_email'   => $email,
      'first_name'   => $first,
      'last_name'    => $last,
      'display_name' => trim($first . ' ' . $last) ?: $email,
      'role'         => $role,
    ]);

    if (is_wp_error($user_id)) {
      wp_send_json_error(['message' => $user_id->get_error_message()]);
    }

    // Dates
    $date_start = ldua_normalize_date_input($_POST['date_start'] ?? '');
    $date_susp  = ldua_normalize_date_input($_POST['date_suspension'] ?? '');
    if ($date_start !== '') update_user_meta($user_id, LDUA_META_ACTIVATION, $date_start);
    else delete_user_meta($user_id, LDUA_META_ACTIVATION);
    if ($date_susp !== '') update_user_meta($user_id, LDUA_META_SUSPENSION, $date_susp);
    else delete_user_meta($user_id, LDUA_META_SUSPENSION);

    // Group + Course
    $group_id  = absint($_POST['group_id'] ?? 0);
    $course_id = absint($_POST['course_id'] ?? 0);

    $groups = $group_id ? [$group_id] : [];
    $courses = $course_id ? [$course_id] : [];

    if (!empty($groups)) {
      if ($role === 'group_leader') {
        ldua_assign_group_leader_to_groups($user_id, $groups);
      } else {
        ldua_enroll_user_to_groups($user_id, $groups);
      }
      // Auto inscrire aux cours du groupe (si apprenant)
      if ($role !== 'group_leader') {
        foreach ($groups as $gid) {
          $gc = ldua_get_group_course_ids($gid);
          if ($gc) $courses = array_values(array_unique(array_merge($courses, $gc)));
        }
      }
    }

    if (!empty($courses) && $role !== 'group_leader') {
      ldua_enroll_user_to_courses($user_id, $courses);
    }

    // 1ère connexion anti-cache
    ldua_mark_first_login_fresh($user_id);

// Email (FR)
$site      = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
$login_url = wp_login_url();
$subject   = "Vos accès à " . $site;

// --- Lien de réinitialisation ---
$reset_url = '';
$user_obj  = get_user_by('id', $user_id);
if ($user_obj instanceof WP_User) {
  $key = get_password_reset_key($user_obj);
  if (!is_wp_error($key)) {
    $reset_url = network_site_url(
      'wp-login.php?action=rp&key=' . rawurlencode($key) . '&login=' . rawurlencode($user_obj->user_login),
      'login'
    );
  }
}

$msg = '
  <div style="font-family:Arial,sans-serif;line-height:1.55">
    <p>Bonjour ' . esc_html(trim($first . ' ' . $last) ?: $email) . ',</p>
    <p>Votre compte vient d’être créé sur <strong>' . esc_html($site) . '</strong>.</p>
    <p><strong>Identifiant :</strong> ' . esc_html($username) . '<br>
       <strong>Mot de passe :</strong> ' . esc_html($password) . '</p>
    <p>Connexion : <a href="' . esc_url($login_url) . '">' . esc_html($login_url) . '</a></p>
    ' . (!empty($reset_url)
      ? '<p>Réinitialiser votre mot de passe : <a href="' . esc_url($reset_url) . '">' . esc_html($reset_url) . '</a></p>'
      : ''
    ) . '
    <p style="color:#666;font-size:12px">Si votre compte n’est pas encore activé, l’accès sera possible à la date d’activation.</p>
  </div>
';

ldua_send_mail_fr($email, $subject, $msg);

wp_send_json_success(['user_id' => (int)$user_id]);

  });

  /* =========================================================
   *  AJAX: delete single
   * ========================================================= */
  add_action('wp_ajax_ldua_delete_user', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);

    $nonce = $_POST['ldua_delete_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_delete_user_ajax')) {
      wp_send_json_error(['message' => 'Nonce invalide.']);
    }

    $user_id = absint($_POST['ldua_delete_user'] ?? 0);
    if (!$user_id) wp_send_json_error(['message' => 'ID invalide.']);

    if ($user_id === get_current_user_id()) wp_send_json_error(['message' => 'Vous ne pouvez pas vous supprimer.']);

    $u = get_userdata($user_id);
    if (!$u) wp_send_json_error(['message' => 'Utilisateur introuvable.']);

    if (in_array('administrator', (array)$u->roles, true)) {
      wp_send_json_error(['message' => 'Suppression admin interdite.']);
    }

    if (!current_user_can('delete_users')) wp_send_json_error(['message' => 'Droit insuffisant.']);

    require_once ABSPATH . 'wp-admin/includes/user.php';
    $ok = wp_delete_user($user_id);
    if (!$ok) wp_send_json_error(['message' => 'Suppression échouée.']);

    wp_send_json_success(['deleted' => [(int)$user_id]]);
  });

  /* =========================================================
   *  AJAX: delete bulk
   * ========================================================= */
  add_action('wp_ajax_ldua_delete_bulk', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);

    $nonce = $_POST['_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_delete_bulk')) {
      wp_send_json_error(['message' => 'Nonce invalide.']);
    }

    if (!current_user_can('delete_users')) wp_send_json_error(['message' => 'Droit insuffisant.']);

    $raw = (string)($_POST['ids'] ?? '');
    $ids = array_values(array_unique(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw)))));
    if (empty($ids)) wp_send_json_error(['message' => 'Aucun ID.']);

    require_once ABSPATH . 'wp-admin/includes/user.php';

    $deleted = [];
    $skipped = [];

    foreach ($ids as $id) {
      if (!$id) continue;
      if ($id === get_current_user_id()) {
        $skipped[] = ['id' => $id, 'reason' => 'self'];
        continue;
      }
      $u = get_userdata($id);
      if (!$u) {
        $skipped[] = ['id' => $id, 'reason' => 'not_found'];
        continue;
      }
      if (in_array('administrator', (array)$u->roles, true)) {
        $skipped[] = ['id' => $id, 'reason' => 'admin'];
        continue;
      }
      $ok = wp_delete_user($id);
      if ($ok) $deleted[] = $id;
      else $skipped[] = ['id' => $id, 'reason' => 'failed'];
    }

    wp_send_json_success(['deleted' => $deleted, 'skipped' => $skipped]);
  });

  /* =========================================================
   *  AJAX: bulk status (activate/suspend)
   * ========================================================= */
  add_action('wp_ajax_ldua_bulk_status', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);

    $nonce = $_POST['_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_bulk_status')) {
      wp_send_json_error(['message' => 'Nonce invalide.']);
    }

    if (!current_user_can('edit_users')) wp_send_json_error(['message' => 'Droit insuffisant.']);

    $mode = sanitize_text_field($_POST['mode'] ?? '');
    if (!in_array($mode, ['activate', 'suspend'], true)) {
      wp_send_json_error(['message' => 'Mode invalide.']);
    }

    $raw = (string)($_POST['ids'] ?? '');
    $ids = array_values(array_unique(array_filter(array_map('absint', preg_split('/\s*,\s*/', $raw)))));
    if (empty($ids)) wp_send_json_error(['message' => 'Aucun ID.']);

    $today = date_i18n('Y-m-d');
    $updated = [];
    $skipped = [];

    foreach ($ids as $id) {
      if (!$id) continue;
      $u = get_userdata($id);
      if (!$u) {
        $skipped[] = ['id' => $id, 'reason' => 'not_found'];
        continue;
      }
      // sécurité : ne jamais toucher aux admins / lms_admin
      if (in_array('administrator', (array)$u->roles, true) || in_array('lms_admin', (array)$u->roles, true)) {
        $skipped[] = ['id' => $id, 'reason' => 'protected_role'];
        continue;
      }

      if ($mode === 'activate') {
        update_user_meta($id, LDUA_META_ACTIVATION, $today);
        delete_user_meta($id, LDUA_META_SUSPENSION);
      } else {
        update_user_meta($id, LDUA_META_SUSPENSION, $today);
        // on garde activation telle quelle
      }

      $st = ldua_get_account_state($id);
      $lc = ldua_state_label_color($st['state']);
      $updated[] = [
        'id' => $id,
        'state' => $st['state'],
        'label' => $lc['label'],
        'color' => $lc['color'],
      ];
    }

    wp_send_json_success(['updated' => $updated, 'skipped' => $skipped]);
  });

  /* =========================================================
   *  AJAX: profile get/save (popup)
   * ========================================================= */
  add_action('wp_ajax_ldua_profile_get', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);
    $nonce = $_POST['_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_profile_ajax')) wp_send_json_error(['message' => 'Nonce invalide.']);

    $id = absint($_POST['user_id'] ?? 0);
    if (!$id) wp_send_json_error(['message' => 'ID invalide.']);
    $u = get_userdata($id);
    if (!$u) wp_send_json_error(['message' => 'Utilisateur introuvable.']);

    $st = ldua_get_account_state($id);
    $lc = ldua_state_label_color($st['state']);

    wp_send_json_success([
      'user' => [
        'id' => $id,
        'login' => $u->user_login,
        'email' => $u->user_email,
        'name'  => $u->display_name,
        'registered' => $u->user_registered,
      ],
      'dates' => [
        'activation' => $st['activation'],
        'suspension' => $st['suspension'],
      ],
      'status' => [
        'state' => $st['state'],
        'label' => $lc['label'],
        'color' => $lc['color'],
      ],
    ]);
  });

  add_action('wp_ajax_ldua_profile_save', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);
    $nonce = $_POST['_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_profile_ajax')) wp_send_json_error(['message' => 'Nonce invalide.']);

    if (!current_user_can('edit_users')) wp_send_json_error(['message' => 'Droit insuffisant.']);

    $id = absint($_POST['user_id'] ?? 0);
    if (!$id) wp_send_json_error(['message' => 'ID invalide.']);
    $u = get_userdata($id);
    if (!$u) wp_send_json_error(['message' => 'Utilisateur introuvable.']);

    // sécurité : pas d’admin/lms_admin
    if (in_array('administrator', (array)$u->roles, true) || in_array('lms_admin', (array)$u->roles, true)) {
      wp_send_json_error(['message' => 'Modification interdite pour ce rôle.']);
    }

    $start = ldua_normalize_date_input($_POST['date_start'] ?? '');
    $sus   = ldua_normalize_date_input($_POST['date_suspension'] ?? '');

    if ($start !== '') update_user_meta($id, LDUA_META_ACTIVATION, $start);
    else delete_user_meta($id, LDUA_META_ACTIVATION);

    if ($sus !== '') update_user_meta($id, LDUA_META_SUSPENSION, $sus);
    else delete_user_meta($id, LDUA_META_SUSPENSION);

    $st = ldua_get_account_state($id);
    $lc = ldua_state_label_color($st['state']);

    wp_send_json_success([
      'status' => [
        'state' => $st['state'],
        'label' => $lc['label'],
        'color' => $lc['color'],
      ],
      'dates' => [
        'activation' => $st['activation'],
        'suspension' => $st['suspension'],
      ],
    ]);
  });

  /* =========================================================
   *  AJAX: import CSV
   * ========================================================= */
  add_action('wp_ajax_ldua_import', function () {
    if (!ldua_can_manage()) wp_send_json_error(['message' => 'Accès refusé.']);

    $nonce = $_POST['ldua_import_ajax_nonce'] ?? '';
    if (!wp_verify_nonce($nonce, 'ldua_import_ajax')) {
      wp_send_json_error(['message' => 'Nonce invalide.']);
    }

    if (empty($_FILES['excel_file']) || !is_uploaded_file($_FILES['excel_file']['tmp_name'])) {
      wp_send_json_error(['message' => 'Fichier manquant.']);
    }

    $tmp = $_FILES['excel_file']['tmp_name'];
    $content = file_get_contents($tmp);
    if ($content === false) wp_send_json_error(['message' => 'Lecture fichier impossible.']);

    // Normaliser encodage + lignes
    $content = preg_replace("/\r\n|\r/", "\n", $content);
    $lines = array_values(array_filter(array_map('trim', explode("\n", $content)), fn($l) => $l !== ''));
    if (count($lines) < 2) wp_send_json_error(['message' => 'CSV vide.']);

    // Détecter séparateur
    $sep = ';';
    if (substr_count($lines[0], ';') < substr_count($lines[0], ',')) $sep = ',';

    $parse = function ($line) use ($sep) {
      $row = str_getcsv($line, $sep);
      return array_map(function ($v) {
        $v = trim((string)$v);
        $v = preg_replace('/^\xEF\xBB\xBF/', '', $v); // BOM
        return $v;
      }, $row);
    };

    $headers = $parse($lines[0]);
    $headers_norm = array_map(fn($h) => ldua_norm($h), $headers);

    // mapping FR + compat EN
    $map = [
      'email' => ['email', 'e-mail', 'mail'],
      'prenom' => ['prenom', 'prénom', 'first name', 'firstname', 'first_name'],
      'nom' => ['nom', 'last name', 'lastname', 'last_name'],
      'identifiant' => ['identifiant', 'login', 'username', 'user_login'],
      'mot_de_passe' => ['mot_de_passe', 'mot de passe', 'password', 'pass'],
      'role' => ['role', 'rôle'],
      'groupes' => ['groupes', 'groups', 'groupe'],
      'parcours' => ['parcours', 'courses', 'course', 'cours'],
      'date_debut' => ['date_debut', 'date debut', 'date_start', 'start_date', 'activation', 'date d\'activation'],
      'date_suspension' => ['date_suspension', 'date suspension', 'suspension', 'suspension_date'],
    ];

    $idx = [];
    foreach ($map as $key => $alts) {
      $idx[$key] = -1;
      foreach ($alts as $alt) {
        $pos = array_search(ldua_norm($alt), $headers_norm, true);
        if ($pos !== false) {
          $idx[$key] = (int)$pos;
          break;
        }
      }
    }

    if ($idx['email'] < 0) wp_send_json_error(['message' => 'Colonne email introuvable.']);

    // lookups group/course titles -> ids
    $groups = get_posts(['post_type' => 'groups', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    $courses = get_posts(['post_type' => 'sfwd-courses', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);

    $group_by_norm_title = [];
    foreach ($groups as $g) $group_by_norm_title[ldua_norm(get_the_title($g->ID))] = (int)$g->ID;

    $course_by_norm_title = [];
    foreach ($courses as $c) $course_by_norm_title[ldua_norm(get_the_title($c->ID))] = (int)$c->ID;

    $parse_ids_or_titles = function ($cell, $title_map) {
      $cell = trim((string)$cell);
      if ($cell === '') return [];
      $parts = preg_split('/\s*[|,]\s*/', $cell);
      $ids = [];
      foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') continue;
        if (is_numeric($p)) {
          $ids[] = (int)$p;
        } else {
          $k = ldua_norm($p);
          if (isset($title_map[$k])) $ids[] = (int)$title_map[$k];
        }
      }
      return array_values(array_unique(array_filter($ids)));
    };

    $created = 0;
    $updated = 0;
    $errors  = 0;
    $messages = [];

    for ($i = 1; $i < count($lines); $i++) {
      $row = $parse($lines[$i]);
      $get = function ($key) use ($row, $idx) {
        $p = $idx[$key] ?? -1;
        return ($p >= 0 && isset($row[$p])) ? $row[$p] : '';
      };

      $email = sanitize_email($get('email'));
      if (!$email || !is_email($email)) {
        $errors++;
        $messages[] = "Ligne " . ($i + 1) . " : email invalide";
        continue;
      }

      $first = sanitize_text_field($get('prenom'));
      $last  = sanitize_text_field($get('nom'));
      $role  = sanitize_text_field($get('role') ?: 'subscriber');
      if (!in_array($role, ['subscriber', 'group_leader', 'lms_admin'], true)) $role = 'subscriber';

      $username = sanitize_user($get('identifiant'), true);
      $password = trim((string)$get('mot_de_passe'));

      $date_start = ldua_normalize_date_input($get('date_debut'));
      $date_susp  = ldua_normalize_date_input($get('date_suspension'));

      $group_ids  = $parse_ids_or_titles($get('groupes'), $group_by_norm_title);
      $course_ids = $parse_ids_or_titles($get('parcours'), $course_by_norm_title);

      $user_id = email_exists($email);

      $is_new = false;
      if (!$user_id) {
        if (!$username) {
          $base = sanitize_user(str_replace(['@', '.'], ['_', '_'], $email), true);
          $username = $base;
          if (username_exists($username)) $username = $base . '_' . wp_generate_password(4, false, false);
        } else {
          if (username_exists($username)) {
            $errors++;
            $messages[] = "Ligne " . ($i + 1) . " : username déjà existant";
            continue;
          }
        }

        if (!$password) $password = wp_generate_password(12, true, false);

        $user_id = wp_insert_user([
          'user_login'   => $username,
          'user_pass'    => $password,
          'user_email'   => $email,
          'first_name'   => $first,
          'last_name'    => $last,
          'display_name' => trim($first . ' ' . $last) ?: $email,
          'role'         => $role,
        ]);

        if (is_wp_error($user_id)) {
          $errors++;
          $messages[] = "Ligne " . ($i + 1) . " : " . $user_id->get_error_message();
          continue;
        }

        $is_new = true;
        $created++;

        ldua_mark_first_login_fresh($user_id);

        // Email uniquement si nouveau
        $site = wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
        $login_url = wp_login_url();
        $subject = "Vos accès à " . $site;
        $msg = '
          <div style="font-family:Arial,sans-serif;line-height:1.55">
            <p>Bonjour ' . esc_html(trim($first . ' ' . $last) ?: $email) . ',</p>
            <p>Votre compte vient d’être créé sur <strong>' . esc_html($site) . '</strong>.</p>
            <p><strong>Identifiant :</strong> ' . esc_html($username) . '<br>
               <strong>Mot de passe :</strong> ' . esc_html($password) . '</p>
            <p>Connexion : <a href="' . esc_url($login_url) . '">' . esc_html($login_url) . '</a></p>
          </div>
        ';
        ldua_send_mail_fr($email, $subject, $msg);
      } else {
        // update simple
        wp_update_user([
          'ID' => (int)$user_id,
          'first_name' => $first ?: get_user_meta($user_id, 'first_name', true),
          'last_name'  => $last ?: get_user_meta($user_id, 'last_name', true),
          'display_name' => (trim($first . ' ' . $last) ?: get_userdata($user_id)->display_name),
          'role' => $role,
        ]);
        $updated++;
      }

      // Dates
      if ($date_start !== '') update_user_meta($user_id, LDUA_META_ACTIVATION, $date_start);
      else delete_user_meta($user_id, LDUA_META_ACTIVATION);

      if ($date_susp !== '') update_user_meta($user_id, LDUA_META_SUSPENSION, $date_susp);
      else delete_user_meta($user_id, LDUA_META_SUSPENSION);

      // Affectations
      if (!empty($group_ids)) {
        if ($role === 'group_leader') {
          ldua_assign_group_leader_to_groups($user_id, $group_ids);
        } else {
          ldua_enroll_user_to_groups($user_id, $group_ids);
        }

        // Auto cours du groupe pour apprenant
        if ($role !== 'group_leader') {
          foreach ($group_ids as $gid) {
            $gc = ldua_get_group_course_ids($gid);
            if ($gc) $course_ids = array_values(array_unique(array_merge($course_ids, $gc)));
          }
        }
      }
      if (!empty($course_ids) && $role !== 'group_leader') {
        ldua_enroll_user_to_courses($user_id, $course_ids);
      }
    }

    wp_send_json_success([
      'created' => $created,
      'updated' => $updated,
      'errors'  => $errors,
      'messages' => $messages,
    ]);
  });

  /* =========================================================
   *  Fallback admin-post (si JS off)
   * ========================================================= */
  add_action('admin_post_ldua_create', function () {
    if (!ldua_can_manage()) ldua_safe_redirect(wp_get_referer(), ['ldua_err' => 'Accès refusé']);
    if (!wp_verify_nonce($_POST['ldua_nonce'] ?? '', 'ldua_create')) ldua_safe_redirect(wp_get_referer(), ['ldua_err' => 'Nonce invalide']);

    // on repasse par l’AJAX interne (mêmes validations) : simple redirect
    ldua_safe_redirect($_POST['ldua_redirect'] ?? wp_get_referer(), ['ldua_err' => 'Active JavaScript pour la création.']);
  });

  add_action('admin_post_ldua_import', function () {
    if (!ldua_can_manage()) ldua_safe_redirect(wp_get_referer(), ['ldua_err' => 'Accès refusé']);
    if (!wp_verify_nonce($_POST['ldua_import_nonce'] ?? '', 'ldua_import')) ldua_safe_redirect(wp_get_referer(), ['ldua_err' => 'Nonce invalide']);
    ldua_safe_redirect($_POST['ldua_redirect'] ?? wp_get_referer(), ['ldua_err' => 'Active JavaScript pour l’import.']);
  });

  add_action('admin_post_ldua_delete', function () {
    if (!ldua_can_manage()) ldua_safe_redirect(wp_get_referer(), ['ldua_err' => 'Accès refusé']);
    if (!wp_verify_nonce($_POST['ldua_delete_nonce'] ?? '', 'ldua_delete')) ldua_safe_redirect(wp_get_referer(), ['ldua_err' => 'Nonce invalide']);
    ldua_safe_redirect($_POST['ldua_redirect'] ?? wp_get_referer(), ['ldua_err' => 'Active JavaScript pour la suppression.']);
  });

  /* =========================================================
   *  Shortcode UI
   * ========================================================= */
  /* ===================== AJAX : Bulk Activer / Suspendre ===================== */
add_action('wp_ajax_ldua_bulk_status', function () {

  if (!is_user_logged_in() || !ldua_can_manage()) {
    wp_send_json_error(['message' => 'Accès refusé.'], 403);
  }

  // ✅ lms_admin a edit_users (normalement), pas toujours manage_options
  if (!current_user_can('edit_users')) {
    wp_send_json_error(['message' => 'Droits insuffisants (edit_users requis).'], 403);
  }

  // ✅ nonce envoyé côté JS via _ajax_nonce
  check_ajax_referer('ldua_bulk_status', '_ajax_nonce');

  $mode = sanitize_text_field($_POST['mode'] ?? '');
  if (!in_array($mode, ['activate', 'suspend'], true)) {
    wp_send_json_error(['message' => 'Mode invalide.'], 400);
  }

  $raw = sanitize_text_field($_POST['ids'] ?? '');
  $ids = array_values(array_unique(array_filter(array_map(
    'intval',
    preg_split('/[,\s]+/', $raw)
  ))));

  if (empty($ids)) {
    wp_send_json_error(['message' => 'Aucun utilisateur sélectionné.'], 400);
  }

  $today = current_time('Y-m-d');
  $updated = [];
  $skipped = [];

  foreach ($ids as $uid) {
    $uid = (int) $uid;
    if (!$uid) continue;

    $u = get_userdata($uid);
    if (!$u) { $skipped[] = $uid; continue; }

    // Sécurité : jamais toucher aux admins / lms_admin
    if (ldua_user_is_exempt_from_dates($uid) || in_array('administrator', (array)$u->roles, true)) {
      $skipped[] = $uid;
      continue;
    }

    if ($mode === 'activate') {
      // ✅ Activer = date d’activation = aujourd’hui + retirer suspension
      update_user_meta($uid, LDUA_META_ACTIVATION, $today);
      delete_user_meta($uid, LDUA_META_SUSPENSION);

    } else { // suspend
      update_user_meta($uid, LDUA_META_SUSPENSION, $today);

      // ✅ Déconnecter immédiatement si déjà connecté
      if (class_exists('WP_Session_Tokens')) {
        WP_Session_Tokens::get_instance($uid)->destroy_all();
      }
    }

    clean_user_cache($uid);

    $st = ldua_get_account_state($uid);
    $updated[] = ['id' => $uid, 'state' => $st['state']];
  }

  wp_send_json_success([
    'mode'    => $mode,
    'updated' => $updated,
    'skipped' => $skipped,
  ]);
});


  add_shortcode('ld_user_admin', function ($atts) {
    if (!ldua_can_manage()) return '<div style="color:#b91c1c">Accès restreint.</div>';

    $per_page = isset($_GET['ldua_per_page']) ? max(1, (int)$_GET['ldua_per_page']) : 20;

    $self_url  = ldua_current_page_url();
    $ep_create = add_query_arg('action', 'ldua_create', admin_url('admin-post.php'));
    $ep_import = add_query_arg('action', 'ldua_import', admin_url('admin-post.php'));
    $ep_delete = add_query_arg('action', 'ldua_delete', admin_url('admin-post.php'));

    $ep_tmpl_ajax = add_query_arg([
      'action' => 'ldua_template_download',
      'ldua_template_nonce' => wp_create_nonce('ldua_template_download'),
    ], admin_url('admin-ajax.php'));

    $ajax_url  = admin_url('admin-ajax.php');
    $ajax_nonce_create      = wp_create_nonce('ldua_create_user_ajax');
    $ajax_nonce_delete      = wp_create_nonce('ldua_delete_user_ajax');
    $ajax_nonce_import      = wp_create_nonce('ldua_import_ajax');
    $ajax_nonce_profile     = wp_create_nonce('ldua_profile_ajax');
    $ajax_nonce_delete_bulk = wp_create_nonce('ldua_delete_bulk');
    $ajax_nonce_bulk_status = wp_create_nonce('ldua_bulk_status');

    $role_labels = [
      'administrator' => 'Administrateur',
      'lms_admin'     => 'Administrateur LMS',
      'group_leader'  => 'Manager',
      'subscriber'    => 'Apprenant',
    ];
    $role_allow_create = [
      'group_leader' => 'Manager',
      'subscriber'   => 'Apprenant',
      'lms_admin'    => 'Administrateur LMS',
    ];

    $all_groups  = get_posts(['post_type' => 'groups', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);
    $all_courses = get_posts(['post_type' => 'sfwd-courses', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC']);

    $notice = '';
    if (!empty($_GET['ldua_msg'])) {
      $map = ['created' => '✅ Utilisateur créé.', 'deleted' => '✅ Utilisateur supprimé.', 'imported' => '✅ Import terminé.', 'updated' => '✅ Mise à jour effectuée.'];
      $m = sanitize_text_field($_GET['ldua_msg']);
      if (isset($map[$m])) $notice .= '<div style="color:#16a34a">' . $map[$m] . '</div>';
    }
    if (!empty($_GET['ldua_err'])) $notice .= '<div style="margin-top:8px;color:#b91c1c">Erreur : ' . esc_html($_GET['ldua_err']) . '</div>';

    // Filtres + pagination
    $f_group    = isset($_GET['ldua_group'])  ? absint($_GET['ldua_group'])  : 0;
    $f_course   = isset($_GET['ldua_course']) ? absint($_GET['ldua_course']) : 0;
    $search     = sanitize_text_field($_GET['ldua_s'] ?? '');
    $f_inactive = sanitize_text_field($_GET['ldua_inactive'] ?? ''); // 1d, 1m, 6m, 1y, ''
    $f_status   = sanitize_text_field($_GET['ldua_status'] ?? ''); // created|active|suspended|''
    if (!in_array($f_status, ['', 'created', 'active', 'suspended'], true)) $f_status = '';

    $have_filter = ($f_group || $f_course || $f_inactive || $f_status);

    $include_ids = [];
    if ($f_group && function_exists('learndash_get_groups_user_ids')) {
      $include_ids = array_map('intval', (array)learndash_get_groups_user_ids($f_group));
    }
    if ($f_course) {
      $course_users = ldua_get_user_ids_for_course($f_course);
      $include_ids  = $include_ids ? array_values(array_intersect($include_ids, $course_users)) : $course_users;
    }

    $paged = max(1, (int)($_GET['ldua_paged'] ?? 1));

    if ($have_filter) {
      if (empty($include_ids)) {
        $include_ids = get_users([
          'fields'       => 'ID',
          'role__not_in' => ['administrator'],
          'number'       => -1,
        ]);
        $include_ids = array_map('intval', (array)$include_ids);
      }

      $ids = $include_ids;

      if ($search) {
        $ids = array_filter($ids, function ($ID) use ($search) {
          $u = get_user_by('id', $ID);
          if (!$u) return false;
          $hay = strtolower($u->user_login . ' ' . $u->user_email . ' ' . $u->display_name);
          return strpos($hay, strtolower($search)) !== false;
        });
      }

      // Exclure admins
      $ids = array_values(array_filter($ids, function ($ID) {
        $u = get_user_by('id', $ID);
        return $u && !in_array('administrator', (array)$u->roles, true);
      }));

      // Filtre statut
      if ($f_status !== '') {
        $ids = array_values(array_filter($ids, function ($ID) use ($f_status) {
          $st = ldua_get_account_state($ID);
          return ($st['state'] === $f_status);
        }));
      }

      // Filtre inactivité
      if ($f_inactive) {
        $now = current_time('timestamp');
        $threshold = null;
        switch ($f_inactive) {
          case '1d':
            $threshold = strtotime('-1 day', $now);
            break;
          case '1m':
            $threshold = strtotime('-1 month', $now);
            break;
          case '6m':
            $threshold = strtotime('-6 months', $now);
            break;
          case '1y':
            $threshold = strtotime('-1 year', $now);
            break;
        }

        if ($threshold) {
          $get_last_seen = function ($ID) {
            $keys = [
              'learndash_last_activity', 'last_activity', 'ld_last_activity',
              'ld_last_login', 'last_login', 'wp_last_login',
            ];
            $last = 0;
            foreach ($keys as $k) {
              $v = get_user_meta($ID, $k, true);
              if ($v === '' || $v === null) continue;
              $t = is_numeric($v) ? (int)$v : strtotime($v);
              if ($t) $last = max($last, $t);
            }
            if (!$last) {
              $u = get_userdata($ID);
              if ($u && $u->user_registered) $last = strtotime($u->user_registered);
            }
            return $last ?: 0;
          };

          $ids = array_filter($ids, function ($ID) use ($threshold, $get_last_seen) {
            $last_seen = $get_last_seen($ID);
            return ($last_seen && $last_seen < $threshold);
          });
        }
      }

      // Filtre date d'inscription
      $start = !empty($_GET['ldua_start']) ? sanitize_text_field($_GET['ldua_start']) : '';
      $end   = !empty($_GET['ldua_end'])   ? sanitize_text_field($_GET['ldua_end'])   : '';
      if ($start || $end) {
        $ids = array_filter($ids, function ($ID) use ($start, $end) {
          $u = get_userdata($ID);
          if (!$u) return false;
          $reg = strtotime($u->user_registered);
          if ($start && $reg < strtotime($start . ' 00:00:00')) return false;
          if ($end   && $reg > strtotime($end . ' 23:59:59'))   return false;
          return true;
        });
      }

      sort($ids, SORT_NUMERIC);
      $total = count($ids);
      $pages = max(1, (int)ceil($total / $per_page));
      $slice = array_slice($ids, ($paged - 1) * $per_page, $per_page);
      $users = array_map(fn($ID) => get_user_by('id', $ID), $slice);
    } else {
      $args = [
        'number'        => $per_page,
        'paged'         => $paged,
        'orderby'       => 'ID',
        'order'         => 'ASC',
        'search'        => $search ? '*' . esc_attr($search) . '*' : '',
        'search_columns' => ['user_login', 'user_email', 'display_name'],
        'fields'        => 'all',
        'role__not_in'  => ['administrator'],
      ];

      $date_query = [];
      $start = !empty($_GET['ldua_start']) ? sanitize_text_field($_GET['ldua_start']) : '';
      $end   = !empty($_GET['ldua_end'])   ? sanitize_text_field($_GET['ldua_end'])   : '';
      if ($start || $end) {
        if ($start) $date_query['after']  = $start;
        if ($end)   $date_query['before'] = date('Y-m-d', strtotime($end . ' +1 day'));
        $args['date_query'] = [$date_query];
      }

      $uq    = new WP_User_Query($args);
      $users = (array)$uq->get_results();
      $total = (int)$uq->get_total();
      $pages = max(1, (int)ceil($total / $per_page));
    }

    $group_name = [];
    foreach ($all_groups as $g) $group_name[$g->ID] = get_the_title($g->ID);

    $uid = 'ldua_' . wp_generate_password(6, false, false);

    ob_start(); ?>
    <div id="<?php echo esc_attr($uid); ?>" class="ldua-wrap" style="max-width:1200px;margin:0 auto;font-size:13px;scroll-margin-top:80px;">
      <?php echo $notice; ?>

      <style>
        .ldua-wrap .ldua-open-profile { color:#1f2937; text-decoration:underline; }
        .ldua-wrap .ldua-open-profile:hover { opacity:.8; }

        /* ---- Ajustements tableau (structure V7.1 : 8 colonnes) ---- */
        .ldua-wrap table.wp-list-table th:nth-child(1),
        .ldua-wrap table.wp-list-table td:nth-child(1) { width: 40px; text-align:center; }

        .ldua-wrap table.wp-list-table th:nth-child(2),
        .ldua-wrap table.wp-list-table td:nth-child(2) { width: 13%; white-space:nowrap; }

        .ldua-wrap table.wp-list-table th:nth-child(3),
        .ldua-wrap table.wp-list-table td:nth-child(3) { width: 16%; }

        /* EMAIL : large + 1 seule ligne */
        .ldua-wrap table.wp-list-table th:nth-child(4),
        .ldua-wrap table.wp-list-table td:nth-child(4) { width: 30%; white-space: nowrap; }

        .ldua-wrap table.wp-list-table th:nth-child(5),
        .ldua-wrap table.wp-list-table td:nth-child(5) { width: 10%; white-space: nowrap; }

        .ldua-wrap table.wp-list-table th:nth-child(6),
        .ldua-wrap table.wp-list-table td:nth-child(6) { width: 12%; white-space: nowrap; }

        .ldua-wrap table.wp-list-table th:nth-child(7),
        .ldua-wrap table.wp-list-table td:nth-child(7) { width: 19%; white-space: normal; }

        .ldua-wrap table.wp-list-table th:nth-child(8),
        .ldua-wrap table.wp-list-table td:nth-child(8) { width: 120px; white-space: nowrap; }

        .ldua-wrap .button.button-small.ldua-open-modal { padding: 2px 8px; font-size: 11.5px; line-height: 1.6; border-radius: 6px; }
        .ldua-wrap.ldua-loading { opacity:.5; pointer-events:none; }

        #ldua-bulk-actions-bar .button { margin-right:8px; }
      </style>

      <h3 style="margin-top:0">👥 UTILISATEURS</h3>

      <form method="get" action="<?php echo esc_url(get_permalink()); ?>" style="margin:0 0 1rem;display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;">
        <label>Groupe :
          <select name="ldua_group">
            <option value="0">Tous</option>
            <?php foreach ($all_groups as $g): ?>
              <option value="<?php echo esc_attr($g->ID); ?>" <?php selected($f_group, $g->ID); ?>><?php echo esc_html(get_the_title($g->ID)); ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Parcours :
          <select name="ldua_course">
            <option value="0">Tous</option>
            <?php foreach ($all_courses as $c): ?>
              <option value="<?php echo esc_attr($c->ID); ?>" <?php selected($f_course, $c->ID); ?>><?php echo esc_html(get_the_title($c->ID)); ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label>Statut :
          <select name="ldua_status">
            <option value="" <?php selected($f_status, ''); ?>>Tous</option>
            <option value="created" <?php selected($f_status, 'created'); ?>>Créé</option>
            <option value="active" <?php selected($f_status, 'active'); ?>>Activé</option>
            <option value="suspended" <?php selected($f_status, 'suspended'); ?>>Suspendu</option>
          </select>
        </label>

        <label>Inactif depuis :
          <select name="ldua_inactive">
            <option value="">—</option>
            <option value="1d" <?php selected($f_inactive, '1d'); ?>>1 jour</option>
            <option value="1m" <?php selected($f_inactive, '1m'); ?>>1 mois</option>
            <option value="6m" <?php selected($f_inactive, '6m'); ?>>6 mois</option>
            <option value="1y" <?php selected($f_inactive, '1y'); ?>>1 an</option>
          </select>
        </label>

        <label>Date d’inscription du :
          <input type="date" name="ldua_start" value="<?php echo esc_attr($_GET['ldua_start'] ?? ''); ?>">
        </label>

        <label>au :
          <input type="date" name="ldua_end" value="<?php echo esc_attr($_GET['ldua_end'] ?? ''); ?>">
        </label>

        <input type="text" name="ldua_s" value="<?php echo esc_attr($search); ?>" placeholder="Recherche (nom/email/login)">
        <input type="hidden" name="ldua_paged" value="1">

        <button type="submit">Filtrer</button>
      </form>

      <div style="overflow:auto">
        <!-- Bulk actions bar -->
        <div id="ldua-bulk-actions-bar" style="display:none;margin-bottom:8px;">
          <button type="button" id="ldua-bulk-delete-btn" class="button button-primary" style="background:#dbd0be;border-color:#dbd0be;">Supprimer</button>
          <button type="button" id="ldua-bulk-activate-btn" class="button button-primary" style="background:#dbd0be;border-color:#dbd0be;">Activer</button>
          <button type="button" id="ldua-bulk-suspend-btn" class="button button-primary" style="background:#dbd0be;border-color:#dbd0be;">Suspendre</button>
        </div>

        <table class="wp-list-table widefat fixed striped" style="min-width:1050px;">
          <thead>
            <tr>
              <th><input type="checkbox" id="ldua-check-all"></th>
              <th>Login</th>
              <th>Nom</th>
              <th>Email</th>
              <th>Statut</th>
              <th>Rôle</th>
              <th>Groupes</th>
              <th>Actions</th>
            </tr>
          </thead>

          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="8">Aucun utilisateur.</td>
              </tr>
              <?php else: foreach ($users as $ru):
                if (!$ru) continue;

                // ✅ Affichage rôle : uniquement Administrateur LMS / Manager / Apprenant
                $roles = (array)$ru->roles;
                if (in_array('lms_admin', $roles, true)) {
                  $role_label = $role_labels['lms_admin'];
                } elseif (in_array('group_leader', $roles, true)) {
                  $role_label = $role_labels['group_leader'];
                } else {
                  $role_label = $role_labels['subscriber'];
                }

                $ugids = function_exists('learndash_get_users_group_ids') ? (array)learndash_get_users_group_ids($ru->ID) : [];
                $ugnames = array_map(fn($gid) => $group_name[$gid] ?? ('#' . $gid), $ugids);

                $me = wp_get_current_user();
                $target_is_admin = in_array('administrator', (array)$ru->roles, true);
                $can_delete = $ru->ID !== get_current_user_id()
                  && !$target_is_admin
                  && user_can($me, 'delete_users');

                $st  = ldua_get_account_state($ru->ID);
                $lc  = ldua_state_label_color($st['state']);
              ?>
                <tr data-user-row="<?php echo (int)$ru->ID; ?>">
                  <td style="text-align:center;"><input type="checkbox" class="ldua-user-checkbox" value="<?php echo (int)$ru->ID; ?>"></td>

                  <td><?php echo esc_html($ru->user_login); ?></td>

                  <td>
                    <a href="#" class="ldua-open-profile" data-user="<?php echo (int)$ru->ID; ?>" data-name="<?php echo esc_attr($ru->display_name); ?>">
                      <?php echo esc_html($ru->display_name); ?>
                    </a>
                  </td>

                  <td class="ldua-email"><?php echo esc_html($ru->user_email); ?></td>

                  <td class="ldua-status-cell">
                    <span class="ldua-status-badge" style="font-weight:700;color:<?php echo esc_attr($lc['color']); ?>">
                      <?php echo esc_html($lc['label']); ?>
                    </span>
                  </td>

                  <td><?php echo esc_html($role_label); ?></td>
                  <td><?php echo esc_html(implode(', ', $ugnames)); ?></td>

                  <td>
                    <?php if ($can_delete): ?>
                      <button type="button" class="ldua-open-modal button button-small" data-user="<?php echo (int)$ru->ID; ?>" data-name="<?php echo esc_attr($ru->display_name); ?>">Supprimer</button>
                    <?php else: ?>—<?php endif; ?>
                  </td>
                </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>

      <?php
      $first_page = 1;
      $last_page  = max(1, (int)$pages);
      $prev_page  = max($first_page, $paged - 1);
      $next_page  = min($last_page, $paged + 1);

      $base_q = array_filter([
        'ldua_group'    => $f_group,
        'ldua_course'   => $f_course,
        'ldua_status'   => $f_status,
        'ldua_s'        => $search,
        'ldua_start'    => $_GET['ldua_start'] ?? '',
        'ldua_end'      => $_GET['ldua_end'] ?? '',
        'ldua_inactive' => $f_inactive,
      ], fn($v) => $v !== '' && $v !== 0);

      $make_url = function ($p) use ($base_q, $uid) {
        $args = array_merge($base_q, [
          'ldua_paged'     => (int)$p,
          'ldua_per_page'  => isset($_GET['ldua_per_page']) ? (int)$_GET['ldua_per_page'] : 20
        ]);
        return add_query_arg($args, get_permalink()) . '#' . $uid;
      };
      ?>

      <div class="ldua-pager" style="margin:14px 0;display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
        <div class="ldua-pager-buttons" style="display:flex;gap:.4rem;flex-wrap:wrap;">
          <?php if ($paged > 1): ?>
            <a href="<?php echo esc_url($make_url($first_page)); ?>" aria-label="Première page" style="padding:.3rem .5rem;border:1px solid #ddd;border-radius:.4rem;display:inline-block;">«</a>
            <a href="<?php echo esc_url($make_url($prev_page)); ?>" aria-label="Page précédente" style="padding:.3rem .5rem;border:1px solid #ddd;border-radius:.4rem;display:inline-block;">‹</a>
          <?php else: ?>
            <span aria-disabled="true" style="padding:.3rem .5rem;border:1px solid #eee;border-radius:.4rem;opacity:.5;display:inline-block;">«</span>
            <span aria-disabled="true" style="padding:.3rem .5rem;border:1px solid #eee;border-radius:.4rem;opacity:.5;display:inline-block;">‹</span>
          <?php endif; ?>

          <span style="padding:.3rem .6rem;border:1px solid #ddd;border-radius:.4rem;background:#f6f6f6;font-weight:700;">
            <?php echo (int)$paged; ?>
          </span>

          <?php if ($paged < $last_page): ?>
            <a href="<?php echo esc_url($make_url($next_page)); ?>" aria-label="Page suivante" style="padding:.3rem .5rem;border:1px solid #ddd;border-radius:.4rem;display:inline-block;">›</a>
            <a href="<?php echo esc_url($make_url($last_page)); ?>" aria-label="Dernière page" style="padding:.3rem .5rem;border:1px solid #ddd;border-radius:.4rem;display:inline-block;">»</a>
          <?php else: ?>
            <span aria-disabled="true" style="padding:.3rem .5rem;border:1px solid #eee;border-radius:.4rem;opacity:.5;display:inline-block;">›</span>
            <span aria-disabled="true" style="padding:.3rem .5rem;border:1px solid #eee;border-radius:.4rem;opacity:.5;display:inline-block;">»</span>
          <?php endif; ?>
        </div>

        <div class="ldua-pager-count" style="color:#666;display:flex;align-items:center;gap:.5rem;">
          <form method="get" action="<?php echo esc_url(get_permalink()); ?>" style="margin:0;">
            <?php
            foreach (['ldua_group', 'ldua_course', 'ldua_status', 'ldua_s', 'ldua_start', 'ldua_end', 'ldua_inactive'] as $keep) {
              if (isset($_GET[$keep])) {
                echo '<input type="hidden" name="' . esc_attr($keep) . '" value="' . esc_attr($_GET[$keep]) . '">';
              }
            }
            ?>
            <label>Afficher :
              <select name="ldua_per_page" onchange="this.form.submit()" style="padding:2px 6px;">
                <?php foreach ([5, 10, 50, 100, 500, 1000] as $n): ?>
                  <option value="<?php echo $n; ?>" <?php selected($_GET['ldua_per_page'] ?? 20, $n); ?>><?php echo $n; ?></option>
                <?php endforeach; ?>
              </select> utilisateurs
            </label>
          </form>
        </div>
      </div>

      <hr style="margin:2rem 0">

      <h3>➕ CRÉER UN UTILISATEUR</h3>
      <form class="ldua-create-form" method="post" action="<?php echo esc_url($ep_create); ?>" style="display:grid;gap:.6rem;max-width:720px;">
        <?php wp_nonce_field('ldua_create', 'ldua_nonce'); ?>
        <input type="hidden" name="ldua_redirect" value="<?php echo esc_url($self_url); ?>">
        <input type="hidden" name="ldua_ajax_nonce" value="<?php echo esc_attr($ajax_nonce_create); ?>">

        <div style="display:grid;gap:.6rem;grid-template-columns:1fr 1fr;">
          <label>Prénom <input type="text" name="first_name"></label>
          <label>Nom <input type="text" name="last_name"></label>
        </div>

        <label>Email* <input type="email" name="email" required></label>

        <div style="display:grid;gap:.6rem;grid-template-columns:1fr 1fr;">
          <label>Identifiant (auto si vide) <input type="text" name="username"></label>
          <label>Mot de passe (auto si vide) <input type="text" name="password"></label>
        </div>

        <label>Rôle
          <select name="role">
            <?php foreach ($role_allow_create as $k => $v): ?>
              <option value="<?php echo esc_attr($k); ?>"><?php echo esc_html($v); ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <?php if (!empty($all_groups)): ?>
          <label>Affecter au groupe
            <select name="group_id">
              <option value="0">— Aucun —</option>
              <?php foreach ($all_groups as $g): ?>
                <option value="<?php echo (int)$g->ID; ?>"><?php echo esc_html(get_the_title($g->ID)); ?></option>
              <?php endforeach; ?>
            </select>
          </label>
        <?php endif; ?>

        <label>Affecter au parcours
          <select name="course_id">
            <option value="0">— Aucun —</option>
            <?php foreach ($all_courses as $c): ?>
              <option value="<?php echo (int)$c->ID; ?>"><?php echo esc_html(get_the_title($c->ID)); ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <?php $today = date_i18n('Y-m-d'); ?>
        <div style="display:grid;gap:.6rem;grid-template-columns:1fr 1fr;">
          <label>Date de début (activation)
            <input type="date" name="date_start" value="<?php echo esc_attr($today); ?>">
            <small style="display:block;color:#666">Laisser vide = compte créé mais non activé.</small>
          </label>
          <label>Date de suspension
            <input type="date" name="date_suspension" value="">
          </label>
        </div>

        <div class="ldua-create-actions" style="display:flex;gap:.5rem;align-items:center;">
          <button type="submit" class="button button-primary" style="padding:6px 12px;font-size:12.5px;border-radius:6px;">Créer</button>
          <span class="ldua-create-status" style="display:none;"></span>
        </div>
      </form>

      <hr style="margin:2rem 0">

      <h3>📥 IMPORT CSV</h3>
      <p>Format accepté : <strong>.csv</strong> (séparateur <strong>;</strong> recommandé). Colonnes :
        <code>email;prenom;nom;identifiant;mot_de_passe;role;groupes;parcours;date_debut;date_suspension</code>.
      </p>
      <p><a href="<?php echo esc_url($ep_tmpl_ajax); ?>" class="button">Télécharger le modèle (CSV)</a></p>

      <form class="ldua-import-form" method="post" enctype="multipart/form-data" action="<?php echo esc_url($ep_import); ?>" style="display:flex;gap:.6rem;align-items:center;flex-wrap:wrap;">
        <?php wp_nonce_field('ldua_import', 'ldua_import_nonce'); ?>
        <input type="hidden" name="ldua_redirect" value="<?php echo esc_url($self_url); ?>">
        <input type="hidden" name="ldua_import_ajax_nonce" value="<?php echo esc_attr($ajax_nonce_import); ?>">
        <input type="file" name="excel_file" accept=".csv" required>
        <button type="submit" class="button button-primary" style="padding:6px 12px;font-size:12.5px;border-radius:6px;">Importer</button>
        <span class="ldua-import-status" style="display:none;margin-left:.5rem;"></span>
      </form>

      <!-- Modal suppression -->
      <div class="ldua-modal" aria-hidden="true" data-mode="single" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:9999;">
        <div class="ldua-modal-card" style="background:#fff;border-radius:12px;max-width:480px;width:92%;box-shadow:0 10px 30px rgba(0,0,0,.2);">
          <div style="padding:18px 20px;border-bottom:1px solid #eee;font-weight:700;">Confirmer la suppression</div>
          <div style="padding:18px 20px;">
            <p class="ldua-modal-text" style="margin:0 0 12px;">Supprimer définitivement l’utilisateur ?</p>
            <form method="post" class="ldua-delete-form" action="<?php echo esc_url($ep_delete); ?>" style="display:flex;gap:.6rem;justify-content:flex-end;margin-top:10px;">
              <?php wp_nonce_field('ldua_delete', 'ldua_delete_nonce'); ?>
              <input type="hidden" name="ldua_redirect" value="<?php echo esc_url($self_url); ?>">
              <input type="hidden" name="ldua_delete_user" value="">
              <input type="hidden" name="ldua_delete_ajax_nonce" value="<?php echo esc_attr($ajax_nonce_delete); ?>">
              <button type="button" class="button ldua-cancel">Annuler</button>
              <button type="submit" class="button button-primary" style="background:#dbd0be;border-color:#dbd0be;">Supprimer</button>
            </form>
          </div>
        </div>
      </div>

      <!-- Modal Activer/Suspendre -->
      <div class="ldua-status-modal" aria-hidden="true" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:9999;">
        <div class="ldua-modal-card" style="background:#fff;border-radius:12px;max-width:480px;width:92%;box-shadow:0 10px 30px rgba(0,0,0,.2);">
          <div class="ldua-status-title" style="padding:18px 20px;border-bottom:1px solid #eee;font-weight:700;">Confirmer</div>
          <div style="padding:18px 20px;">
            <p class="ldua-status-text" style="margin:0 0 12px;"></p>
            <div style="display:flex;gap:.6rem;justify-content:flex-end;margin-top:10px;">
              <button type="button" class="button ldua-status-cancel">Annuler</button>
              <button type="button" class="button button-primary ldua-status-confirm" style="background:#dbd0be;border-color:#dbd0be;">Confirmer</button>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal profil utilisateur -->
      <div class="ldua-profile-modal" aria-hidden="true" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:10000;">
        <div style="background:#fff;border-radius:12px;max-width:560px;width:92%;box-shadow:0 10px 30px rgba(0,0,0,.2);">
          <div style="padding:18px 20px;border-bottom:1px solid #eee;font-weight:700;display:flex;justify-content:space-between;align-items:center;">
            <span class="ldua-profile-title">Profil utilisateur</span>
            <button type="button" class="button ldua-profile-close">Fermer</button>
          </div>

          <div style="padding:18px 20px;display:grid;gap:.6rem;">
            <input type="hidden" class="ldua-profile-user-id" value="">

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
              <label>Login
                <input type="text" class="ldua-prof-login" readonly>
              </label>
              <label>Email
                <input type="text" class="ldua-prof-email" readonly>
              </label>
            </div>

            <label>Nom
              <input type="text" class="ldua-prof-name" readonly>
            </label>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
              <label>Date d’inscription
                <input type="text" class="ldua-prof-registered" readonly>
              </label>
              <label>Statut
                <input type="text" class="ldua-prof-status" readonly>
              </label>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.6rem;">
              <label>Date de début (activation)
                <input type="date" class="ldua-prof-date-start">
              </label>
              <label>Date de suspension
                <input type="date" class="ldua-prof-date-suspension">
              </label>
            </div>

            <div style="display:flex;gap:.6rem;justify-content:flex-end;margin-top:.6rem;">
              <button type="button" class="button button-primary ldua-prof-save" style="background:#dbd0be;border-color:#dbd0be;">Enregistrer</button>
            </div>

            <div class="ldua-prof-msg" style="display:none;color:#16a34a;font-weight:700;"></div>
          </div>
        </div>
      </div>

      <script>
        (function () {
          const root = document.getElementById('<?php echo esc_js($uid); ?>');
          if (!root) return;

          function lduaNavigate(url, push = true) {
            try {
              const target = new URL(url, window.location.href);
              root.classList.add('ldua-loading');
              fetch(target.toString(), { credentials: 'same-origin' })
                .then(r => r.text())
                .then(html => {
                  const doc = new DOMParser().parseFromString(html, 'text/html');
                  const next = doc.querySelector('.ldua-wrap');
                  if (!next) { window.location.assign(target.toString()); return; }
                  const wrapper = document.createElement('div');
                  wrapper.innerHTML = next.innerHTML;
                  wrapper.querySelectorAll('script').forEach(s => s.remove());
                  root.innerHTML = wrapper.innerHTML;
                  if (push) history.pushState({ ldua: true }, '', target.toString());
                  lduaInit(root);
                  root.scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(() => window.location.assign(target.toString()))
                .finally(() => root.classList.remove('ldua-loading'));
            } catch (e) { window.location.assign(url); }
          }

          function selectedIds() {
            return Array.from(root.querySelectorAll('.ldua-user-checkbox:checked')).map(cb => cb.value);
          }

          function updateBulkBar() {
            const bar = root.querySelector('#ldua-bulk-actions-bar');
            if (!bar) return;
            const ids = selectedIds();
            bar.style.display = (ids.length > 0) ? 'block' : 'none';
          }

          function setAllChecked(val) {
            root.querySelectorAll('.ldua-user-checkbox').forEach(cb => cb.checked = val);
            updateBulkBar();
          }

          function updateRowStatus(id, label, color) {
            const row = root.querySelector('tr[data-user-row="' + id + '"]');
            if (!row) return;
            const badge = row.querySelector('.ldua-status-badge');
            if (!badge) return;
            badge.textContent = label || '';
            badge.style.color = color || '#111';
          }

          function openDeleteModal(idsOrId, label) {
            const modal = root.querySelector('.ldua-modal');
            const form = modal.querySelector('.ldua-delete-form');
            const input = form.querySelector('input[name="ldua_delete_user"]');

            if (Array.isArray(idsOrId)) {
              modal.dataset.mode = 'bulk';
              input.value = idsOrId.join(',');
              modal.querySelector('.ldua-modal-text').textContent =
                'Supprimer définitivement ' + idsOrId.length + ' utilisateur(s) sélectionné(s) ?';
            } else {
              modal.dataset.mode = 'single';
              input.value = idsOrId;
              modal.querySelector('.ldua-modal-text').textContent =
                'Supprimer définitivement l’utilisateur "' + (label || ('#' + idsOrId)) + '" (#' + idsOrId + ') ?';
            }

            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');

            modal.onclick = (ev) => {
              if (ev.target === modal) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
              }
            };
            modal.querySelector('.ldua-cancel').onclick = () => {
              modal.style.display = 'none';
              modal.setAttribute('aria-hidden', 'true');
            };
          }

          function openStatusModal(mode, ids) {
            const modal = root.querySelector('.ldua-status-modal');
            const title = modal.querySelector('.ldua-status-title');
            const text = modal.querySelector('.ldua-status-text');
            const confirm = modal.querySelector('.ldua-status-confirm');

            modal.dataset.mode = mode;
            modal.dataset.ids = ids.join(',');

            if (mode === 'activate') {
              title.textContent = 'Confirmer l’activation';
              text.textContent = 'Activer ' + ids.length + ' utilisateur(s) ? (date d’activation = aujourd’hui, suspension effacée)';
            } else {
              title.textContent = 'Confirmer la suspension';
              text.textContent = 'Suspendre ' + ids.length + ' utilisateur(s) ? (date de suspension = aujourd’hui)';
            }

            modal.style.display = 'flex';
            modal.setAttribute('aria-hidden', 'false');

            modal.onclick = (ev) => {
              if (ev.target === modal) {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
              }
            };
            modal.querySelector('.ldua-status-cancel').onclick = () => {
              modal.style.display = 'none';
              modal.setAttribute('aria-hidden', 'true');
            };

            confirm.onclick = () => {
              const params = new URLSearchParams();
              params.append('action', 'ldua_bulk_status');
              params.append('mode', mode);
              params.append('ids', ids.join(','));
              params.append('_ajax_nonce', '<?php echo esc_js($ajax_nonce_bulk_status); ?>');

              fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: params })
                .then(r => r.json())
                .then(data => {
                  if (data && data.success) {
                    (data.data.updated || []).forEach(u => updateRowStatus(u.id, u.label, u.color));
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                    setAllChecked(false);
                  } else {
                    alert((data && data.data && data.data.message) ? data.data.message : 'Erreur inconnue');
                  }
                })
                .catch(() => alert('Erreur réseau'));
            };
          }

          function openProfile(userId) {
            const modal = root.querySelector('.ldua-profile-modal');
            const msg = modal.querySelector('.ldua-prof-msg');
            msg.style.display = 'none';
            msg.textContent = '';

            const params = new URLSearchParams();
            params.append('action', 'ldua_profile_get');
            params.append('user_id', userId);
            params.append('_ajax_nonce', '<?php echo esc_js($ajax_nonce_profile); ?>');

            fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: params })
              .then(r => r.json())
              .then(data => {
                if (!data || !data.success) {
                  alert((data && data.data && data.data.message) ? data.data.message : 'Erreur');
                  return;
                }
                const u = data.data.user;
                const d = data.data.dates;
                const st = data.data.status;

                modal.querySelector('.ldua-profile-user-id').value = u.id;
                modal.querySelector('.ldua-prof-login').value = u.login || '';
                modal.querySelector('.ldua-prof-email').value = u.email || '';
                modal.querySelector('.ldua-prof-name').value = u.name || '';
                modal.querySelector('.ldua-prof-registered').value = u.registered || '';
                modal.querySelector('.ldua-prof-status').value = st.label || '';

                modal.querySelector('.ldua-prof-date-start').value = d.activation || '';
                modal.querySelector('.ldua-prof-date-suspension').value = d.suspension || '';

                modal.style.display = 'flex';
                modal.setAttribute('aria-hidden', 'false');
              })
              .catch(() => alert('Erreur réseau'));
          }

          function lduaInit(root) {
            // Check all + bulk bar
            const checkAll = root.querySelector('#ldua-check-all');
            if (checkAll) {
              checkAll.addEventListener('change', () => setAllChecked(checkAll.checked));
            }
            root.querySelectorAll('.ldua-user-checkbox').forEach(cb => {
              cb.addEventListener('change', updateBulkBar);
            });
            updateBulkBar();

            // Bulk buttons
            const bulkDel = root.querySelector('#ldua-bulk-delete-btn');
            if (bulkDel) {
              bulkDel.addEventListener('click', () => {
                const ids = selectedIds();
                if (!ids.length) return;
                openDeleteModal(ids);
              });
            }

            const bulkAct = root.querySelector('#ldua-bulk-activate-btn');
            if (bulkAct) {
              bulkAct.addEventListener('click', () => {
                const ids = selectedIds();
                if (!ids.length) return;
                openStatusModal('activate', ids);
              });
            }

            const bulkSus = root.querySelector('#ldua-bulk-suspend-btn');
            if (bulkSus) {
              bulkSus.addEventListener('click', () => {
                const ids = selectedIds();
                if (!ids.length) return;
                openStatusModal('suspend', ids);
              });
            }

            // Single delete modal open
            root.querySelectorAll('.ldua-open-modal').forEach(btn => {
              btn.addEventListener('click', e => {
                e.preventDefault();
                openDeleteModal(btn.getAttribute('data-user'), btn.getAttribute('data-name'));
              });
            });

            // Delete submit (single + bulk via modal)
            const delForm = root.querySelector('.ldua-delete-form');
            if (delForm) {
              delForm.addEventListener('submit', function (ev) {
                try {
                  ev.preventDefault();
                  const raw = (delForm.querySelector('input[name="ldua_delete_user"]').value || '').trim();
                  if (!raw) return;

                  const modal = root.querySelector('.ldua-modal');
                  const isBulk = raw.includes(',') || (modal && modal.dataset.mode === 'bulk');

                  if (isBulk) {
                    const ids = raw.split(',').map(s => s.trim()).filter(Boolean);

                    const params = new URLSearchParams();
                    params.append('action', 'ldua_delete_bulk');
                    params.append('ids', ids.join(','));
                    params.append('_ajax_nonce', '<?php echo esc_js($ajax_nonce_delete_bulk); ?>');

                    fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: params })
                      .then(r => r.json())
                      .then(data => {
                        if (data && data.success) {
                          (data.data.deleted || ids).forEach(id => {
                            const row = root.querySelector('tr[data-user-row="' + id + '"]');
                            if (row) row.remove();
                          });
                          if (modal) { modal.style.display = 'none'; modal.setAttribute('aria-hidden', 'true'); }
                          const checkAll = root.querySelector('#ldua-check-all');
                          if (checkAll) checkAll.checked = false;
                          updateBulkBar();
                        } else {
                          alert((data && data.data && data.data.message) ? data.data.message : 'Erreur inconnue');
                        }
                      })
                      .catch(() => { alert('Erreur réseau'); });

                    return;
                  }

                  // Single delete (AJAX)
                  const fd = new FormData(delForm);
                  fd.append('action', 'ldua_delete_user');
                  fd.append('ldua_delete_ajax_nonce', delForm.querySelector('input[name="ldua_delete_ajax_nonce"]').value || '');

                  fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                    .then(r => r.json())
                    .then(data => {
                      if (data && data.success) {
                        const id = raw;
                        const row = root.querySelector('tr[data-user-row="' + id + '"]');
                        if (row) row.parentNode.removeChild(row);
                        if (modal) { modal.style.display = 'none'; modal.setAttribute('aria-hidden', 'true'); }
                        updateBulkBar();
                      } else {
                        alert((data && data.data && data.data.message) ? data.data.message : 'Erreur inconnue');
                      }
                    })
                    .catch(() => { alert('Erreur réseau'); });

                } catch (e) {
                  delForm.submit();
                }
              });
            }
/* ========= BULK ACTIVER / SUSPENDRE ========= */
(function(){
  const statusModal = root.querySelector('.ldua-status-modal');
  if(!statusModal) return;

  const titleEl   = statusModal.querySelector('.ldua-status-title');
  const textEl    = statusModal.querySelector('.ldua-status-text');
  const cancelBtn = statusModal.querySelector('.ldua-status-cancel');
  const confirmBtn= statusModal.querySelector('.ldua-status-confirm');

  const bulkActivateBtn = root.querySelector('#ldua-bulk-activate-btn');
  const bulkSuspendBtn  = root.querySelector('#ldua-bulk-suspend-btn');

  function selectedIds(){
    return Array.from(root.querySelectorAll('.ldua-user-checkbox:checked'))
      .map(cb => (cb.value || '').trim())
      .filter(Boolean);
  }

  function openStatusModal(mode, ids){
    statusModal.dataset.mode = mode;
    statusModal.dataset.ids  = ids.join(',');

    if(mode === 'activate'){
      titleEl.textContent = 'Confirmer l’activation';
      textEl.textContent  = 'Activer immédiatement ' + ids.length + ' utilisateur(s) sélectionné(s) ?';
    } else {
      titleEl.textContent = 'Confirmer la suspension';
      textEl.textContent  = 'Suspendre immédiatement ' + ids.length + ' utilisateur(s) sélectionné(s) ?';
    }

    statusModal.style.display = 'flex';
    statusModal.setAttribute('aria-hidden','false');
  }

  function closeStatusModal(){
    statusModal.style.display = 'none';
    statusModal.setAttribute('aria-hidden','true');
  }

  function applyStatusToRow(id, state, label){
    const row = root.querySelector('tr[data-user-row="'+id+'"]');
    if(!row) return;

    const span = row.querySelector('td:nth-child(5) span'); // colonne Statut
    if(!span) return;

    let color = '#b45309';           // créé
    if(state === 'active') color = '#16a34a';
    if(state === 'suspended') color = '#b91c1c';

    span.textContent = label;
    span.style.color = color;
    span.style.fontWeight = '700';
  }

  // Ouvrir modal
  if (bulkActivateBtn){
    bulkActivateBtn.addEventListener('click', ()=>{
      const ids = selectedIds();
      if(!ids.length) return;
      openStatusModal('activate', ids);
    });
  }

  if (bulkSuspendBtn){
    bulkSuspendBtn.addEventListener('click', ()=>{
      const ids = selectedIds();
      if(!ids.length) return;
      openStatusModal('suspend', ids);
    });
  }

  // Fermer modal
  statusModal.addEventListener('click', (ev)=>{ if(ev.target === statusModal) closeStatusModal(); });
  if (cancelBtn) cancelBtn.addEventListener('click', closeStatusModal);

  // Confirmer => AJAX
  if (confirmBtn){
    confirmBtn.addEventListener('click', ()=>{
      const mode = statusModal.dataset.mode || '';
      const ids  = (statusModal.dataset.ids || '').split(',').map(s=>s.trim()).filter(Boolean);
      if(!mode || !ids.length) return;

const params = new URLSearchParams();
params.append('action', 'ldua_bulk_status');
params.append('mode', mode);              // ✅ IMPORTANT
params.append('ids', ids.join(','));      // ✅ IMPORTANT
params.append('_ajax_nonce', '<?php echo esc_js($ajax_nonce_bulk_status); ?>');

fetch('<?php echo esc_url($ajax_url); ?>', {
  method: 'POST',
  credentials: 'same-origin',
  body: params
})
.then(r => r.json())
.then(data => {
  if (!data || !data.success) {
    alert((data && data.data && data.data.message) ? data.data.message : 'Erreur inconnue');
    return;
  }

  // ✅ Mettre à jour la colonne "Statut" dans le tableau immédiatement
  (data.data.updated || []).forEach(item => {
    const row = root.querySelector('tr[data-user-row="'+item.id+'"]');
    if (!row) return;

    const cell = row.querySelector('td:nth-child(5)'); // colonne Statut
    if (!cell) return;

    const state = item.state;
    const label = (state === 'active') ? 'Activé' : (state === 'suspended') ? 'Suspendu' : 'Créé';
    const color = (state === 'active') ? '#16a34a' : (state === 'suspended') ? '#b91c1c' : '#b45309';
    cell.innerHTML = '<span style="font-weight:700;color:'+color+'">'+label+'</span>';
  });

  // fermer modal + reset sélection
  const m = root.querySelector('.ldua-status-modal');
  if (m){ m.style.display='none'; m.setAttribute('aria-hidden','true'); }

  const checkAll = root.querySelector('#ldua-check-all');
  if (checkAll) checkAll.checked = false;
  root.querySelectorAll('.ldua-user-checkbox').forEach(cb => cb.checked = false);
  if (typeof updateBulkBar === 'function') updateBulkBar();
})
.catch(() => alert('Erreur réseau'));

    });
  }
})();

            // Creation
            const createForm = root.querySelector('.ldua-create-form');
            if (createForm) {
              createForm.addEventListener('submit', function (ev) {
                try {
                  ev.preventDefault();
                  const status = createForm.querySelector('.ldua-create-status');
                  const btn = createForm.querySelector('button[type="submit"]');
                  const fd = new FormData(createForm);
                  fd.append('action', 'ldua_create_user');
                  fd.append('ldua_ajax_nonce', createForm.querySelector('input[name="ldua_ajax_nonce"]').value || '');
                  status.style.display = 'inline-block';
                  status.textContent = 'Création en cours…';
                  btn.disabled = true;

                  fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                    .then(r => r.json())
                    .then(data => {
                      btn.disabled = false;
                      if (data && data.success) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('ldua_msg', 'created');
                        lduaNavigate(url.toString(), false);
                      } else {
                        status.textContent = '❌ ' + ((data && data.data && data.data.message) || 'Erreur inconnue');
                      }
                    })
                    .catch(() => {
                      btn.disabled = false;
                      status.textContent = '❌ Erreur réseau';
                    });
                } catch (e) { createForm.submit(); }
              });
            }

            // Import
            const importForm = root.querySelector('.ldua-import-form');
            if (importForm) {
              importForm.addEventListener('submit', function (ev) {
                try {
                  ev.preventDefault();
                  const status = importForm.querySelector('.ldua-import-status');
                  const btn = importForm.querySelector('button[type="submit"]');
                  const fd = new FormData(importForm);
                  fd.append('action', 'ldua_import');
                  fd.append('ldua_import_ajax_nonce', importForm.querySelector('input[name="ldua_import_ajax_nonce"]').value || '');

                  status.style.display = 'inline-block';
                  status.style.color = '#111';
                  status.textContent = 'Import en cours…';
                  btn.disabled = true;

                  fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: fd })
                    .then(r => r.json())
                    .then(data => {
                      btn.disabled = false;
                      if (data && data.success) {
                        const c = data.data.created || 0;
                        const u = data.data.updated || 0;
                        const e = data.data.errors || 0;
                        status.style.color = (e > 0) ? '#b91c1c' : '#16a34a';
                        status.textContent = '✅ Import terminé — créés: ' + c + ', mis à jour: ' + u + ', erreurs: ' + e;

                        const url = new URL(window.location.href);
                        url.searchParams.set('ldua_msg', 'imported');
                        lduaNavigate(url.toString(), false);
                      } else {
                        status.style.color = '#b91c1c';
                        status.textContent = '❌ ' + ((data && data.data && data.data.message) || 'Erreur inconnue');
                      }
                    })
                    .catch(() => {
                      btn.disabled = false;
                      status.style.color = '#b91c1c';
                      status.textContent = '❌ Erreur réseau';
                    });
                } catch (e) { importForm.submit(); }
              });
            }

            // Profile open
            root.querySelectorAll('.ldua-open-profile').forEach(a => {
              a.addEventListener('click', (e) => {
                e.preventDefault();
                openProfile(a.getAttribute('data-user'));
              });
            });

            // Profile close + overlay click
            const profModal = root.querySelector('.ldua-profile-modal');
            if (profModal) {
              const close = profModal.querySelector('.ldua-profile-close');
              close.onclick = () => {
                profModal.style.display = 'none';
                profModal.setAttribute('aria-hidden', 'true');
              };
              profModal.onclick = (ev) => {
                if (ev.target === profModal) {
                  profModal.style.display = 'none';
                  profModal.setAttribute('aria-hidden', 'true');
                }
              };

              // Save
              const saveBtn = profModal.querySelector('.ldua-prof-save');
              saveBtn.onclick = () => {
                const userId = profModal.querySelector('.ldua-profile-user-id').value;
                const start = profModal.querySelector('.ldua-prof-date-start').value;
                const sus = profModal.querySelector('.ldua-prof-date-suspension').value;
                const msg = profModal.querySelector('.ldua-prof-msg');

                const params = new URLSearchParams();
                params.append('action', 'ldua_profile_save');
                params.append('user_id', userId);
                params.append('date_start', start || '');
                params.append('date_suspension', sus || '');
                params.append('_ajax_nonce', '<?php echo esc_js($ajax_nonce_profile); ?>');

                fetch('<?php echo esc_url($ajax_url); ?>', { method: 'POST', credentials: 'same-origin', body: params })
                  .then(r => r.json())
                  .then(data => {
                    if (data && data.success) {
                      msg.style.display = 'block';
                      msg.style.color = '#16a34a';
                      msg.textContent = '✅ Enregistré';
                      const st = data.data.status;
                      profModal.querySelector('.ldua-prof-status').value = st.label || '';
                      updateRowStatus(userId, st.label, st.color);
                    } else {
                      msg.style.display = 'block';
                      msg.style.color = '#b91c1c';
                      msg.textContent = '❌ ' + ((data && data.data && data.data.message) || 'Erreur');
                    }
                  })
                  .catch(() => {
                    msg.style.display = 'block';
                    msg.style.color = '#b91c1c';
                    msg.textContent = '❌ Erreur réseau';
                  });
              };
            }
          }

          // init
          lduaInit(root);

          // SPA back/forward
          window.addEventListener('popstate', (e) => {
            if (e.state && e.state.ldua) lduaNavigate(location.href, false);
          });
        })();
      </script>
    </div>
<?php
    return ob_get_clean();
  });
}


// === Stats LearnDash ===
/**
 * [ld_stat type="enrolled|completed|remaining|certificates|lessons_completed|interactions" user_id=""]
 * - enrolled        : nb de cours inscrits
 * - completed       : nb de cours terminés
 * - remaining       : nb de cours restants
 * - certificates    : nb de certificats de COURS obtenus
 * - lessons_completed: nb de leçons LearnDash terminées (tous cours confondus)
 * - interactions    : commentaires LD + messages bbPress (topics + replies)
 */
add_shortcode('ld_stat', function($atts){

  $a = shortcode_atts([
    'type'    => 'enrolled',
    'user_id' => get_current_user_id(),
  ], $atts, 'ld_stat');

  $uid = (int) $a['user_id'];
  if ( ! $uid ) return '0';

  // ============== COURS INSCRITS (IDs) ==============
  if ( ! function_exists('learndash_user_get_enrolled_courses') ) return '0';
  $course_ids = learndash_user_get_enrolled_courses($uid, [
    'num'         => -1,
    'post_status' => 'publish',
    'return'      => 'ids',
  ]);
  if ( ! is_array($course_ids) ) $course_ids = [];

  $enrolled  = count($course_ids);
  $completed = 0;
  $certs     = 0;

  // ============== BOUCLE COURS ==============
  foreach ( $course_ids as $cid ) {

    // Progression fiable (même logique que le widget profil LD)
    if ( function_exists('learndash_course_progress') ) {
      $progress = learndash_course_progress([
        'user_id'   => $uid,
        'course_id' => $cid,
        'array'     => true,
      ]);
      $is_completed = ! empty($progress['completed']);
    } else {
      $is_completed = function_exists('learndash_is_course_complete') ? learndash_is_course_complete($uid, $cid) : false;
    }

    if ( $is_completed ) {
      $completed++;

      // Certificat de COURS ?
      $cert_id = function_exists('learndash_get_setting') ? learndash_get_setting($cid, 'certificate') : 0;
      if ( $cert_id ) {
        if ( function_exists('learndash_get_course_certificate_link') ) {
          $link = learndash_get_course_certificate_link($cid, $uid);
        } else {
          $link = function_exists('learndash_user_get_certificate_link') ? learndash_user_get_certificate_link($cid, $uid) : '';
        }
        if ( ! empty($link) ) $certs++;
      }
    }
  }

  // ============== LEÇONS COMPLÉTÉES (tous cours) ==============
  $lessons_completed = 0;
  if ( ! empty($course_ids) && function_exists('learndash_get_course_lessons_list') ) {
    foreach ( $course_ids as $cid ) {
      $lessons = learndash_get_course_lessons_list($cid, $uid);
      if ( is_array($lessons) ) {
        foreach ( $lessons as $row ) {
          if ( ! empty($row['post']->ID) ) {
            $lid = (int) $row['post']->ID;
            // Vérifie le statut réel de la leçon
            if ( function_exists('learndash_is_lesson_complete') && learndash_is_lesson_complete($uid, $lid, $cid) ) {
              $lessons_completed++;
            }
          }
        }
      }
    }
  }
  
  
  // ============== INTERACTIONS (commentaires LD + forum bbPress) ==============
  $interactions = 0;

  // (a) Commentaires sur les contenus LearnDash
  $ld_post_types = ['sfwd-courses','sfwd-lessons','sfwd-topic','sfwd-quiz'];
  if ( class_exists('WP_Comment_Query') ) {
    $args = [
      'user_id'   => $uid,
      'status'    => 'approve',          // change en '' si tu veux tout statut
      'count'     => true,               // renvoie un entier
      'post_type' => $ld_post_types,
    ];
    $cq = new WP_Comment_Query();
    $interactions += (int) $cq->query($args);
  }

  // (b) Forum bbPress : topics + replies postés par l'utilisateur
  if ( function_exists('post_type_exists') ) {
    if ( post_type_exists('topic') ) $interactions += (int) get_usernumposts($uid, 'topic');
    if ( post_type_exists('reply') ) $interactions += (int) get_usernumposts($uid, 'reply');
  }

  // ============== SORTIE ==============
  $remaining = max(0, $enrolled - $completed);

  switch ( strtolower($a['type']) ) {
    case 'enrolled':           return (string) $enrolled;
    case 'completed':          return (string) $completed;
    case 'remaining':          return (string) $remaining;
    case 'certificates':       return (string) $certs;
    case 'lessons_completed':  return (string) $lessons_completed;
    case 'interactions':       return (string) $interactions;
    default:                   return '0';
  }
});

// === LD – Time tracking + hours ===


add_action('wp_enqueue_scripts', function () {
  if ( ! is_user_logged_in() ) return;

  if ( ! is_singular( array('sfwd-courses','sfwd-lessons','sfwd-topic','sfwd-quiz') ) ) return;

  $uid      = get_current_user_id();
  $post_id  = get_the_ID();
  $course_id = 0;

  if ( function_exists('learndash_get_course_id') ) {
    $course_id = (int) learndash_get_course_id( $post_id );
    if ( ! $course_id && get_post_type($post_id) === 'sfwd-courses' ) {
      $course_id = (int) $post_id; // on est sur la page du cours
    }
  }

  wp_register_script('ld-time-track', false, array(), null, true);
  wp_enqueue_script('ld-time-track');

  $data = array(
    'ajaxUrl'  => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('ld_time_nonce'),
    'interval' => 15,              // en secondes: envoie 15s quand l'onglet est visible
    'courseId' => $course_id,
  );

  $inline = 'window.LDTime='.wp_json_encode($data).';
  (function(){
    function send(){
      if(document.visibilityState!=="visible") return;
      var body="action=ld_track_time&nonce="+encodeURIComponent(LDTime.nonce)
              +"&seconds="+LDTime.interval+"&course_id="+LDTime.courseId;
      if (navigator.sendBeacon) {
        try {
          var f=new FormData();
          f.append("action","ld_track_time");
          f.append("nonce",LDTime.nonce);
          f.append("seconds",LDTime.interval);
          f.append("course_id",LDTime.courseId);
          navigator.sendBeacon(LDTime.ajaxUrl,f); 
          return;
        } catch(e){}
      }
      fetch(LDTime.ajaxUrl,{method:"POST",credentials:"same-origin",
        headers:{"Content-Type":"application/x-www-form-urlencoded"},body:body});
    }
    setInterval(send, LDTime.interval*1000);
  })();';

  wp_add_inline_script('ld-time-track', $inline);
}, 20);

/* Reçoit le temps et sauve en user_meta (total + par cours) */
add_action('wp_ajax_ld_track_time', function () {
  check_ajax_referer('ld_time_nonce','nonce');
  if ( ! is_user_logged_in() ) wp_send_json_error();

  $uid      = get_current_user_id();
  $seconds  = isset($_POST['seconds']) ? max(0, intval($_POST['seconds'])) : 0;
  $courseId = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;

  if ($seconds <= 0) wp_send_json_success();

  // total
  $total = (int) get_user_meta($uid, 'ld_total_seconds', true);
  update_user_meta($uid, 'ld_total_seconds', $total + $seconds);

  // par cours
  if ($courseId) {
    $key = 'ld_course_seconds_' . $courseId;
    $cur = (int) get_user_meta($uid, $key, true);
    update_user_meta($uid, $key, $cur + $seconds);
  }

  wp_send_json_success();
});

/* Utilitaire format heures */
function ld_seconds_to_hours($seconds, $decimals = 1){
  return number_format( max(0,(int)$seconds)/3600, (int)$decimals, ',', ' ' );
}

/* Shortcode total heures: [ld_hours decimals="1" user_id=""] */
add_shortcode('ld_hours', function($atts){
  $a = shortcode_atts(['decimals'=>1,'user_id'=>get_current_user_id()], $atts);
  $s = (int) get_user_meta( (int)$a['user_id'], 'ld_total_seconds', true );
  return ld_seconds_to_hours($s, (int)$a['decimals']);
});

/* Shortcode heures d’un cours: [ld_hours_course id="123" decimals="1" user_id=""] */
add_shortcode('ld_hours_course', function($atts){
  $a = shortcode_atts(['id'=>0,'decimals'=>1,'user_id'=>get_current_user_id()], $atts);
  $cid = (int) $a['id'];
  if ( ! $cid && function_exists('learndash_get_course_id') ) {
    $cid = (int) learndash_get_course_id( get_the_ID() );
    if ( ! $cid && get_post_type() === 'sfwd-courses' ) $cid = (int) get_the_ID();
  }
  if ( ! $cid ) return '0';
  $s = (int) get_user_meta( (int)$a['user_id'], 'ld_course_seconds_'.$cid, true );
  return ld_seconds_to_hours($s, (int)$a['decimals']);
});

// === LD – Groupes de l’utilisateur ===

add_shortcode('ld_user_groups', function($atts){
  $a = shortcode_atts([
    'user_id' => get_current_user_id(),
    'link'    => 'no',          // "yes" pour lier vers la page du groupe
    'format'  => 'inline',      // "list" pour <ul><li>...</li></ul>
    'sep'     => ', ',          // séparateur en mode inline
    'first'   => 'no',          // "yes" pour n’afficher que le 1er groupe
    'empty'   => 'Aucun groupe'
  ], $atts, 'ld_user_groups');

  $uid = (int) $a['user_id'];
  if (!$uid || !function_exists('learndash_get_users_group_ids')) return '';

  $ids = learndash_get_users_group_ids($uid, true);
  if (empty($ids)) return esc_html($a['empty']);

  if (strtolower($a['first']) === 'yes') $ids = array_slice($ids, 0, 1);

  $items = [];
  foreach ($ids as $gid) {
    $name = get_the_title($gid);
    if (strtolower($a['link']) === 'yes') {
      $items[] = '<a href="'.esc_url(get_permalink($gid)).'">'.esc_html($name).'</a>';
    } else {
      $items[] = esc_html($name);
    }
  }

  if (strtolower($a['format']) === 'list') {
    $out = '<ul class="ld-user-groups">';
    foreach ($items as $it) $out .= '<li>'.$it.'</li>';
    return $out.'</ul>';
  }

  $sep = wp_kses_post($a['sep']);
  return implode($sep, $items);
});

// === cert design ===
/**
 * [ld_certificates user_id="" empty="Aucun certificat pour le moment."]
 * - Liste les certificats de COURS obtenus par l'utilisateur
 * - Affichage en cartes/boutons + bouton "Télécharger"
 */
add_shortcode('ld_certificates', function($atts){
  $a = shortcode_atts([
    'user_id' => get_current_user_id(),
    'empty'   => __('Aucun certificat pour le moment.', 'ld'),
  ], $atts, 'ld_certificates');

  $uid = (int) $a['user_id'];
  if (!$uid || ! function_exists('learndash_user_get_enrolled_courses')) return '';

  // CSS une seule fois
  static $printed = false;
  if (!$printed) {
    $printed = true; ?>
    <style>
      :root{
        /* Palette personnalisée */
        --c-primary:#2E3143;     /* texte / titres / contours foncés */
        --c-accent:#dbd0be;      /* boutons / états d'action */
        --c-info:#BCDFFE;        /* badges / accents doux */
        --c-bg:#ffffff;          /* fond carte */
        --c-bd:#E6ECF6;          /* bordure douce */
        --c-muted:#6b7a90;       /* meta text */
        --c-shadow:0 10px 20px rgba(20,38,60,.06);
      }

      .ld-certs{
        display:grid;grid-template-columns:1fr;gap:14px
      }

      .ld-cert{
        display:flex;align-items:center;justify-content:space-between;
        width:100%;
        background:var(--c-bg);
        border:1px solid var(--c-bd);
        border-radius:16px;
        padding:16px 18px;
        box-shadow:var(--c-shadow);
        transition:.2s ease;
        text-decoration:none
      }
      .ld-cert:hover{
        transform:translateY(-2px);
        box-shadow:0 12px 24px rgba(20,38,60,.09)
      }

      .ld-cert__left{
        display:flex;align-items:center;gap:14px;min-width:0
      }
      .ld-cert__badge{
        flex:0 0 auto;width:40px;height:40px;border-radius:12px;
        background:var(--c-info);
        border:1px solid #a8c9fb; /* bordure légèrement plus foncée que #BCDFFE */
        color:var(--c-primary);
        display:flex;align-items:center;justify-content:center;
        font-weight:900
      }
      .ld-cert__title{
        margin:0;font-size:1.05rem;font-weight:800;
        color:var(--c-primary);line-height:1.2
      }
      .ld-cert__meta{
        font-size:.9rem;color:var(--c-muted);margin-top:2px
      }

      .ld-cert__actions{
        display:flex;align-items:center;gap:10px;flex:0 0 auto
      }
.ld-cert__btn {
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  background: var(--c-accent);
  color: #fff; /* texte blanc */
  font-weight: 800;
  padding: .55rem .9rem;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,.05);
  transition: background .2s ease, transform .15s ease;
}
.ld-cert__btn:hover {
  background: #dbd0be; /* accent un peu plus foncé au hover */
  transform: translateY(-1px);
}
.ld-cert__btn svg {
  width: 18px;
  height: 18px;
}
.ld-cert__btn svg path {
  stroke: #fff; /* flèche blanche */
}


      /* lien carte entier -> style bouton (mobile: rien à changer) */
      .ld-cert__cta{display:none}

      /* États vides (carte placeholder) */
      .ld-cert--empty .ld-cert__badge{
        background:#f3f7ff;border-color:var(--c-bd);color:var(--c-primary)
      }
      .ld-cert--empty .ld-cert__title{color:var(--c-primary)}

      @media (max-width:680px){
        .ld-cert{padding:14px}
        .ld-cert__title{font-size:1rem}
      }
    </style>
    <?php
  }

  // Récupère tous les cours inscrits
  $courses = learndash_user_get_enrolled_courses($uid, [
    'num'         => -1,
    'post_status' => 'publish',
    'return'      => 'ids',
  ]);
  if (!is_array($courses)) $courses = [];

  $items = [];

  foreach ($courses as $cid) {
    // Le cours a un certificat ?
    $cert_post_id = function_exists('learndash_get_setting') ? (int) learndash_get_setting($cid, 'certificate') : 0;
    if (! $cert_post_id) continue;

    // Lien du certificat pour cet utilisateur
    $link = '';
    if (function_exists('learndash_get_course_certificate_link')) {
      $link = learndash_get_course_certificate_link($cid, $uid);
    } elseif (function_exists('learndash_user_get_certificate_link')) {
      $link = learndash_user_get_certificate_link($cid, $uid);
    }
    if (empty($link)) continue; // pas encore obtenu

    $title = get_the_title($cid);
    $items[] = [
      'title' => $title,
      'link'  => esc_url($link),
    ];
  }

  if (empty($items)) {
    return '<div class="ld-certs"><div class="ld-cert ld-cert--empty" style="justify-content:flex-start">'
          .'<div class="ld-cert__left"><span class="ld-cert__badge">—</span>'
          .'<div><div class="ld-cert__title">'.esc_html($a['empty']).'</div></div></div></div></div>';
  }

  ob_start(); ?>
  <div class="ld-certs">
    <?php foreach ($items as $it): ?>
      <a class="ld-cert" href="<?php echo $it['link']; ?>" target="_blank" rel="noopener">
        <div class="ld-cert__left">
          <span class="ld-cert__badge">C</span>
          <div>
            <div class="ld-cert__title"><?php echo esc_html($it['title']); ?></div>
            <div class="ld-cert__meta">Certificat disponible</div>
          </div>
        </div>
        <div class="ld-cert__actions">
          <span class="ld-cert__btn">
            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            Télécharger
          </span>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
  <?php
  return ob_get_clean();
});

// === Remplace tin canny ===
/* Renommer Tin Canny Reports -> DataLab dans l'UI (traduction à la volée) */
add_filter('gettext', function ($translated, $text, $domain) {

  // Le text-domain peut varier selon la version : on ne filtre pas par domain.
  if (stripos($translated, 'Tin Canny Reports') !== false) {
    $translated = str_ireplace('Tin Canny Reports', 'DataLab', $translated);
  }
  // (optionnel) tu peux renommer d'autres libellés ici si besoin :
  // if (stripos($translated, 'Course Report') !== false) { $translated = 'Rapport Cours'; }
  // if (stripos($translated, 'User Report')   !== false) { $translated = 'Rapport Utilisateur'; }

  return $translated;
}, 20, 3);

// === admin cert ===
/**
 * [ld_admin_certificates allow_group_leaders="no|yes" show="both|courses|quizzes"]
 * V4 — multi-sélection en ZIP (fichiers conservent leur nom), ZIP nommé certificat_YYYY-MM-DD_HH-mm.zip
 * UI: colonne Type supprimée, thead centré, typo plus petite, colonne Groupes élargie,
 * bouton "Télécharger" masqué tant qu'aucune sélection.
 */
add_shortcode('ld_admin_certificates', function($atts){
  if (!function_exists('learndash_get_course_certificate_link')) {
    return '<div style="color:#b91c1c">LearnDash requis.</div>';
  }

  $a = shortcode_atts([
    'allow_group_leaders' => 'no',
    'show'                => 'both', // both|courses|quizzes
  ], $atts, 'ld_admin_certificates');

  $current = wp_get_current_user();
  $is_admin = user_can($current, 'manage_options') || user_can($current, 'manage_sites');
  $is_group_leader = function_exists('ld_is_group_leader') ? ld_is_group_leader($current->ID) : user_can($current, 'group_leader');

  if (!$is_admin && !($a['allow_group_leaders']==='yes' && $is_group_leader)) {
    return '<div style="color:#b91c1c">Accès restreint.</div>';
  }

  // Groupes visibles
  $groups_args = ['post_type'=>'groups','posts_per_page'=>-1,'post_status'=>'any','orderby'=>'title','order'=>'ASC','fields'=>'ids'];
  if ($a['allow_group_leaders']==='yes' && $is_group_leader && !$is_admin && function_exists('learndash_get_administrators_group_ids')) {
    $leader_groups = (array) learndash_get_administrators_group_ids($current->ID);
    if (empty($leader_groups)) return '<div>Aucun groupe assigné.</div>';
    $groups_args['post__in'] = $leader_groups;
  }
  $group_ids_all = get_posts($groups_args);

  // Tous les parcours (cours)
  $all_course_ids = get_posts(['post_type'=>'sfwd-courses','posts_per_page'=>-1,'fields'=>'ids','orderby'=>'title','order'=>'ASC']);

  $selected_group_id  = isset($_GET['ld_group'])  ? absint($_GET['ld_group'])  : 0;
  $selected_course_id = isset($_GET['ld_course']) ? absint($_GET['ld_course']) : 0;
  if ($selected_group_id && !get_post($selected_group_id))   $selected_group_id = 0;
  if ($selected_course_id && !get_post($selected_course_id)) $selected_course_id = 0;

  // Tous les utilisateurs
  $user_ids = array_map('intval', (array) get_users(['fields'=>'ID']));

  // Si Group Leader : limiter aux membres de ses groupes
  if ($a['allow_group_leaders']==='yes' && $is_group_leader && !$is_admin && function_exists('learndash_get_administrators_group_ids')) {
    $lgroups = (array) learndash_get_administrators_group_ids($current->ID);
    $allowed_users = [];
    foreach ($lgroups as $gid) {
      if (function_exists('learndash_get_groups_user_ids')) {
        $allowed_users = array_merge($allowed_users, (array) learndash_get_groups_user_ids($gid));
      }
    }
    $user_ids = !empty($allowed_users)
      ? array_values(array_intersect($user_ids, array_map('intval',$allowed_users)))
      : [];
  }

  // Map user -> groupes
  $user_groups_map = [];
  foreach ($user_ids as $uid) {
    $ugs = function_exists('learndash_get_users_group_ids') ? (array) learndash_get_users_group_ids($uid) : [];
    $user_groups_map[$uid] = $ugs;
  }

  // Index groupes pour labels
  $groups_titles = [];
  foreach ($group_ids_all as $gid) $groups_titles[$gid] = get_the_title($gid);

  // Tous les quiz
  $all_quiz_ids = get_posts(['post_type'=>'sfwd-quiz','posts_per_page'=>-1,'fields'=>'ids']);

  // Helpers dates
  $course_completed_date = function($user_id, $course_id){
    if (function_exists('learndash_user_get_course_completed_date')) {
      $ts = learndash_user_get_course_completed_date($user_id, $course_id);
      if (!empty($ts)) return date_i18n(get_option('date_format'), intval($ts));
    }
    $meta = get_user_meta($user_id, 'course_completed_'.intval($course_id), true);
    if (!empty($meta)) {
      $ts = is_numeric($meta) ? intval($meta) : strtotime($meta);
      if ($ts) return date_i18n(get_option('date_format'), $ts);
    }
    return '';
  };
  $quiz_pass_date = function($user_id, $quiz_id){
    $date_str = '';
    if (function_exists('learndash_get_user_quiz_attempts')) {
      $attempts = learndash_get_user_quiz_attempts($user_id, $quiz_id);
      if (empty($attempts)) $attempts = learndash_get_user_quiz_attempts($user_id, $quiz_id, 0);
      if (!empty($attempts)) {
        foreach ($attempts as $att) {
          if (!empty($att['pass']) && !empty($att['time_completed'])) {
            $date_str = date_i18n(get_option('date_format'), intval($att['time_completed']));
          }
        }
      }
    }
    return $date_str;
  };
  $quiz_parent_course = function($quiz_id){
    if (function_exists('learndash_get_course_id')) {
      $cid = (int) learndash_get_course_id($quiz_id);
      if ($cid) return $cid;
    }
    $cid = (int) get_post_meta($quiz_id, 'course_id', true);
    if ($cid) return $cid;
    $cid = (int) get_post_meta($quiz_id, 'ld_course_id', true);
    return $cid ?: 0;
  };

  // Lignes
  $rows = [];
  foreach ($user_ids as $uid) {
    $user_group_ids = isset($user_groups_map[$uid]) ? (array)$user_groups_map[$uid] : [];

    // COURS
    if ($a['show']==='both' || $a['show']==='courses') {
      foreach ($all_course_ids as $course_id) {
        $link = learndash_get_course_certificate_link($course_id, $uid);
        if (!$link) continue;
        $date = $course_completed_date($uid, $course_id);
        $rows[] = [
          'user_id'   => $uid,
          'display'   => get_the_author_meta('display_name', $uid),
          'email'     => get_the_author_meta('user_email', $uid),
          'group_ids' => $user_group_ids,
          'type'      => 'Cours',   // conservé, mais non affiché
          'course_id' => $course_id,
          'title'     => get_the_title($course_id),
          'date'      => $date,
          'link'      => $link,
        ];
      }
    }

    // QUIZ
    if ($a['show']==='both' || $a['show']==='quizzes') {
      if (function_exists('learndash_get_quiz_certificate_link')) {
        foreach ($all_quiz_ids as $quiz_id) {
          $q_link = learndash_get_quiz_certificate_link($quiz_id, $uid);
          if (!$q_link) continue;
          $date = $quiz_pass_date($uid, $quiz_id);
          $cid  = $quiz_parent_course($quiz_id);
          $rows[] = [
            'user_id'   => $uid,
            'display'   => get_the_author_meta('display_name', $uid),
            'email'     => get_the_author_meta('user_email', $uid),
            'group_ids' => $user_group_ids,
            'type'      => 'Quiz',   // conservé, mais non affiché
            'course_id' => $cid,     // pour filtre parcours
            'title'     => get_the_title($quiz_id),
            'date'      => $date,
            'link'      => $q_link,
          ];
        }
      }
    }
  }

  // ----- Rendu -----
  $container_id = 'ld-cert-admin-'.uniqid();
  $nonce = wp_create_nonce('ldac_zip');
  $ajax  = admin_url('admin-ajax.php');

  ob_start(); ?>
  <div id="<?php echo esc_attr($container_id); ?>" class="ld-cert-admin-wrap">

    <!-- Filtres -->
    <form method="get" class="ld-cert-filters" style="margin:0 0 .8rem; display:flex; gap:.5rem; align-items:center; flex-wrap:wrap;">
      <label for="ld_group">Groupe :</label>
      <select id="ld_group" name="ld_group" onchange="this.form.submit()">
        <option value="0">Tous les groupes</option>
        <?php foreach ($group_ids_all as $gid): ?>
          <option value="<?php echo esc_attr($gid); ?>" <?php selected($selected_group_id, $gid); ?>>
            <?php echo esc_html(get_the_title($gid)); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <label for="ld_course">Parcours :</label>
      <select id="ld_course" name="ld_course" onchange="this.form.submit()">
        <option value="0">Tous les parcours</option>
        <?php foreach ($all_course_ids as $cid): ?>
          <option value="<?php echo esc_attr($cid); ?>" <?php selected($selected_course_id, $cid); ?>>
            <?php echo esc_html(get_the_title($cid)); ?>
          </option>
        <?php endforeach; ?>
      </select>

      <?php
      // préserver autres paramètres
      foreach ($_GET as $k=>$v) {
        if (in_array($k, ['ld_group','ld_course'], true)) continue;
        if (is_array($v)) continue;
        echo '<input type="hidden" name="'.esc_attr($k).'" value="'.esc_attr($v).'">';
      }
      ?>
      <noscript><button type="submit">Filtrer</button></noscript>
    </form>

    <!-- Barre d'actions : cachée tant qu'aucune sélection -->
    <div class="ld-bulk-bar" style="margin:.3rem 0 .6rem; display:none; gap:.5rem; align-items:center; flex-wrap:wrap;">
      <button type="button" class="button button-primary ld-bulk-download">Télécharger</button>
    </div>

    <div class="ld-cert-table-wrap" style="overflow:auto">

      <!-- Styles SCOPÉS -->
      <style>
        #<?php echo esc_html($container_id); ?> .ld-cert-table{
          font-size: 13px;
        }
        #<?php echo esc_html($container_id); ?> .ld-cert-table thead th{
          font-size: 12.5px;
          text-transform: uppercase;
          letter-spacing: .02em;
          text-align: center;
          vertical-align: middle;
          padding: .55rem .5rem !important;
        }
        #<?php echo esc_html($container_id); ?> .ld-cert-table tbody td{
          font-size: 13px;
          vertical-align: middle;
        }
        /* largeur + wrap pour la colonne Groupes (4e colonne) */
        #<?php echo esc_html($container_id); ?> .ld-cert-table thead th:nth-child(4),
        #<?php echo esc_html($container_id); ?> .ld-cert-table tbody td:nth-child(4){
          width: 26%;
          white-space: normal;
          line-height: 1.35;
        }
        /* centre la colonne checkbox (1re) et Certificat (7e) */
        #<?php echo esc_html($container_id); ?> .ld-cert-table thead th:nth-child(1),
        #<?php echo esc_html($container_id); ?> .ld-cert-table tbody td:nth-child(1),
        #<?php echo esc_html($container_id); ?> .ld-cert-table thead th:nth-child(7),
        #<?php echo esc_html($container_id); ?> .ld-cert-table tbody td:nth-child(7){
          text-align: center !important;
        }
      </style>

      <table class="ld-cert-table" style="width:100%; border-collapse:collapse;">
        <thead>
          <tr>
            <th style="width:34px; border-bottom:1px solid #eee; padding:.5rem;">
              <input type="checkbox" class="ld-select-all" title="Tout sélectionner">
            </th>
            <th style="border-bottom:1px solid #eee; padding:.5rem;">Utilisateur</th>
            <th style="border-bottom:1px solid #eee; padding:.5rem;">Email</th>
            <th style="border-bottom:1px solid #eee; padding:.5rem;">Groupe(s)</th>
            <th style="border-bottom:1px solid #eee; padding:.5rem;">Intitulé</th>
            <th style="border-bottom:1px solid #eee; padding:.5rem;">Date</th>
            <th style="border-bottom:1px solid #eee; padding:.5rem;">Certificat</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $printed = 0;
        foreach ($rows as $r):
          if ($selected_group_id && !in_array($selected_group_id, (array)$r['group_ids'], true)) continue;
          if ($selected_course_id && (int)$r['course_id'] !== (int)$selected_course_id) continue;
          $printed++;
          $glabels = array_map(function($gid) use ($groups_titles){
            return isset($groups_titles[$gid]) ? $groups_titles[$gid] : '#'.$gid;
          }, (array)$r['group_ids']);
        ?>
          <tr>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5; text-align:center;">
              <?php if (!empty($r['link'])): ?>
                <input type="checkbox" class="ld-cert-select" value="<?php echo esc_url($r['link']); ?>" />
              <?php endif; ?>
            </td>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5;"><?php echo esc_html($r['display']); ?></td>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5;"><?php echo esc_html($r['email']); ?></td>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5;"><?php echo esc_html(implode(', ', $glabels)); ?></td>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5;"><?php echo esc_html($r['title']); ?></td>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5;"><?php echo esc_html($r['date']); ?></td>
            <td style="padding:.5rem; border-bottom:1px solid #f5f5f5; text-align:center;">
              <?php if (!empty($r['link'])): ?>
                <a class="ld-cert-link" href="<?php echo esc_url($r['link']); ?>" target="_blank" rel="noopener">Voir</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$printed): ?>
          <tr><td colspan="7" style="padding:.8rem;">Aucun certificat trouvé pour ce filtre.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script>
  (function(){
    const root = document.getElementById('<?php echo esc_js($container_id); ?>');
    if (!root) return;

    const selectAll   = root.querySelector('.ld-select-all');
    const bulkBar     = root.querySelector('.ld-bulk-bar');
    const downloadBtn = root.querySelector('.ld-bulk-download');
    const ajaxUrl     = '<?php echo esc_js($ajax); ?>';
    const nonce       = '<?php echo esc_js($nonce); ?>';

    const getCbs = ()=> Array.from(root.querySelectorAll('.ld-cert-select'));
    const getSelected = ()=> getCbs().filter(cb=>cb.checked).map(cb=>cb.value);

    function updateBulk(){
      const hasSel = getSelected().length>0;
      bulkBar.style.display = hasSel ? 'flex' : 'none';
      if (selectAll){
        const cbs = getCbs();
        const allChecked = cbs.length>0 && cbs.every(cb=>cb.checked);
        selectAll.checked = allChecked;
        selectAll.indeterminate = !allChecked && cbs.some(cb=>cb.checked);
      }
    }

    if (selectAll){
      selectAll.addEventListener('change', e=>{
        getCbs().forEach(cb=> cb.checked = e.target.checked);
        updateBulk();
      });
    }
    root.addEventListener('change', e=>{
      if (e.target && e.target.classList.contains('ld-cert-select')) updateBulk();
    });
    updateBulk();

    function triggerDownload(url){
      const a = document.createElement('a');
      a.href = url;
      a.setAttribute('download', '');
      a.style.display='none';
      document.body.appendChild(a);
      a.click();
      a.remove();
    }

    if (downloadBtn){
      downloadBtn.addEventListener('click', ()=>{
        const links = getSelected();
        if (!links.length){ return; }
        downloadBtn.disabled = true; downloadBtn.textContent = 'Préparation…';

        const form = new FormData();
        form.append('action','ldac_zip_certs');
        form.append('_ajax_nonce', nonce);
        form.append('links', JSON.stringify(links));

        fetch(ajaxUrl, { method:'POST', credentials:'same-origin', body: form })
          .then(r=>r.json())
          .then(res=>{
            if (res && res.success && res.data && res.data.url){
              triggerDownload(res.data.url);
            } else {
              alert((res && res.data && res.data.message) ? res.data.message : 'Erreur lors de la création du ZIP.');
            }
          })
          .catch(()=> alert('Erreur réseau lors de la création du ZIP.'))
          .finally(()=>{ downloadBtn.disabled=false; downloadBtn.textContent='Télécharger'; });
      });
    }
  })();
  </script>
  <?php
  return ob_get_clean();
});


/** ================== AJAX : crée un ZIP avec les liens sélectionnés ================== */
if (!function_exists('ldac_zip_certs_cb')) {
  add_action('wp_ajax_ldac_zip_certs', 'ldac_zip_certs_cb');
  function ldac_zip_certs_cb(){
    check_ajax_referer('ldac_zip');

    $current = wp_get_current_user();
    $is_admin = user_can($current, 'manage_options') || user_can($current, 'manage_sites');
    $is_group_leader = function_exists('ld_is_group_leader') ? ld_is_group_leader($current->ID) : user_can($current, 'group_leader');
    if (!$is_admin && !$is_group_leader) {
      wp_send_json_error(['message'=>'Autorisations insuffisantes.'], 403);
    }

    if (empty($_POST['links'])) wp_send_json_error(['message'=>'Aucun lien fourni.'], 400);
    $links = json_decode(stripslashes((string) $_POST['links']), true);
    if (!is_array($links) || empty($links)) wp_send_json_error(['message'=>'Format de liens invalide.'], 400);

    if (!class_exists('ZipArchive')) {
      wp_send_json_error(['message'=>'Extension PHP ZipArchive manquante sur le serveur.'], 500);
    }

    $uploads = wp_upload_dir();
    if (empty($uploads['basedir']) || empty($uploads['baseurl'])) {
      wp_send_json_error(['message'=>'Répertoire uploads introuvable.'], 500);
    }

    $base_dir = trailingslashit($uploads['basedir']).'ldac_zips';
    wp_mkdir_p($base_dir);

    // Nom du dossier temporaire et du ZIP (date/heure du téléchargement)
    $stamp   = current_time('Y-m-d_H-i');
    $batch   = 'batch_'.get_current_user_id().'_'.$stamp.'_'.wp_generate_password(3,false,false);
    $batch_dir = trailingslashit($base_dir).$batch;
    wp_mkdir_p($batch_dir);

    $saved_files = [];
    $used_names  = [];
    $site_host   = parse_url(home_url(), PHP_URL_HOST);

    // Cookies d'auth si les URLs sont protégées
    $cookie_header = '';
    foreach ($_COOKIE as $k=>$v){
      if (strpos($k, 'wordpress_logged_in_') === 0){
        $cookie_header .= $k.'='.rawurlencode($v).'; ';
      }
    }

    foreach ($links as $i => $url){
      $url = esc_url_raw($url);
      if (!$url) continue;

      // Sécurité : même domaine que le site
      $host = parse_url($url, PHP_URL_HOST);
      if ($host && $site_host && $host !== $site_host) continue;

      $args = ['timeout'=>45];
      if ($cookie_header) $args['headers'] = ['cookie'=>$cookie_header];

      $res = wp_remote_get($url, $args);
      if (is_wp_error($res)) continue;
      $code = wp_remote_retrieve_response_code($res);
      if ((int)$code !== 200) continue;
      $body = wp_remote_retrieve_body($res);
      if (empty($body)) continue;

      // ----- Déterminer le NOM DE FICHIER d’origine -----
      $filename = '';
      // 1) Content-Disposition: attachment; filename="xxxx.pdf"
      $cd = wp_remote_retrieve_header($res, 'content-disposition');
      if ($cd){
        // récupérer filename= ou filename*= (RFC 5987)
        if (preg_match('/filename\*?=(?:UTF-8\'\')?"?([^";]+)"?/i', $cd, $m)) {
          $filename = $m[1];
          $filename = rawurldecode($filename);
        } elseif (preg_match('/filename="?([^";]+)"?/i', $cd, $m2)){
          $filename = $m2[1];
        }
      }
      // 2) Sinon, basename du path d’URL
      if (!$filename){
        $path = parse_url($url, PHP_URL_PATH);
        $base = $path ? basename($path) : '';
        $filename = $base ?: 'certificat.pdf';
      }
      // 3) Nettoyage / extension
      $filename = sanitize_file_name($filename);
      $ext = pathinfo($filename, PATHINFO_EXTENSION);
      if (!$ext){
        $ct = wp_remote_retrieve_header($res, 'content-type');
        $ext = (strpos((string)$ct,'pdf')!==false) ? 'pdf' : 'bin';
        $filename .= '.'.$ext;
      }

      // Eviter doublons dans le ZIP
      $base_noext = preg_replace('/\.[^.]+$/', '', $filename);
      $ext_withdot = '.' . (pathinfo($filename, PATHINFO_EXTENSION) ?: 'pdf');
      $candidate = $filename;
      $suffix = 2;
      while (in_array($candidate, $used_names, true)) {
        $candidate = $base_noext.'-'.$suffix.$ext_withdot;
        $suffix++;
      }
      $filename = $candidate;
      $used_names[] = $filename;

      // Sauvegarder le fichier temporaire
      $fpath = trailingslashit($batch_dir).$filename;
      file_put_contents($fpath, $body);
      $saved_files[] = $fpath;
    }

    if (empty($saved_files)) {
      wp_send_json_error(['message'=>'Impossible de récupérer les certificats sélectionnés.'], 500);
    }

    // Nom du ZIP : certificat_YYYY-MM-DD_HH-mm.zip
    $zip_basename = 'certificat_'.$stamp.'.zip';
    $zip_path = trailingslashit($batch_dir).$zip_basename;

    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE)!==TRUE){
      wp_send_json_error(['message'=>'Échec de création du ZIP.'], 500);
    }
    foreach ($saved_files as $f){
      $zip->addFile($f, basename($f)); // <-- conserve le nom d’origine déterminé ci-dessus
    }
    $zip->close();

    $zip_url = trailingslashit($uploads['baseurl']).'ldac_zips/'.$batch.'/'.$zip_basename;
    wp_send_json_success(['url'=>$zip_url], 200);
  }
}

// === Email ===
/**
 * Plugin Name: LD Notify Manager (front, safe)
 * Description: [ld_notify_manager] — Activer/Désactiver les notifications LearnDash côté front. Accès: administrator + lms_admin. Version blindée.
 * Version:     1.3.0
 */

if (!defined('ABSPATH')) exit;

if (!class_exists('LDN_Notify_Manager')) {
  class LDN_Notify_Manager {
    private static $instance = null;
    private $cpt = ''; // Slug du CPT Notifications une fois détecté

    public static function instance(){
      if (self::$instance === null) self::$instance = new self();
      return self::$instance;
    }

    private function __construct(){
      // Détecter le CPT après l'enregistrement des CPT
      add_action('init', [$this, 'detect_cpt'], 20);

      // Shortcode
      add_shortcode('ld_notify_manager', [$this, 'shortcode']);

      // Handler AJAX (front)
      add_action('wp_ajax_ldn_toggle', [$this, 'ajax_toggle']);

      // Donner virtuellement les caps à lms_admin sur CE CPT uniquement
      add_filter('map_meta_cap', [$this, 'grant_caps_for_lms_admin'], 10, 4);
    }

    /** Rôles autorisés à voir/agir */
    private function can_manage(){
      if (!is_user_logged_in()) return false;
      $u = wp_get_current_user();
      $allowed = apply_filters('ldn_allowed_roles', ['administrator','lms_admin']);
      return (bool) array_intersect($allowed, (array) $u->roles);
    }

    /** Détection du CPT Notifications (à l'init) */
    public function detect_cpt(){
      $candidates = [
        'ld-notification',
        'ld-notifications',
        'ld_notifications',
        'learndash-notification',
        'learndash-notifications'
      ];
      foreach ($candidates as $pt) {
        if (post_type_exists($pt)) {
          $this->cpt = $pt;
          break;
        }
      }
    }

    /** Filtre des caps : donner à lms_admin edit/publish/read sur CE CPT seulement */
    public function grant_caps_for_lms_admin($caps, $cap, $user_id, $args){
      if (empty($args[0])) return $caps;
      if (!in_array($cap, ['edit_post','publish_post','read_post'], true)) return $caps;

      $post = get_post((int)$args[0]);
      if (!$post || empty($this->cpt) || $post->post_type !== $this->cpt) return $caps;

      $u = get_user_by('id', $user_id);
      if ($u && in_array('lms_admin', (array)$u->roles, true)) {
        // Accorder via une primitive que tout utilisateur connecté possède
        return ['read'];
      }
      return $caps;
    }

    /** Shortcode renderer */
    public function shortcode($atts){
      if (!$this->can_manage()) {
        return '<div style="color:#b91c1c">Accès restreint.</div>';
      }

      if (empty($this->cpt)) {
        return '<div style="color:#b91c1c">Type de contenu des notifications introuvable. Vérifie que LearnDash Notifications est activé.</div>';
      }

      $a = shortcode_atts(['per_page' => -1], $atts, 'ld_notify_manager');

      // URL de retour (page du shortcode)
      $back_url = '';
      if (function_exists('get_queried_object_id')) {
        $qid = get_queried_object_id();
        if ($qid) $back_url = get_permalink($qid);
      }
      if (empty($back_url) && !empty($_SERVER['REQUEST_URI'])) {
        $back_url = home_url(add_query_arg([], $_SERVER['REQUEST_URI']));
      }
      if (empty($back_url)) {
        $ref = wp_get_referer();
        if ($ref) $back_url = $ref;
      }
      if (empty($back_url)) $back_url = home_url('/');

      // Notices
      $notice = '';
      if (!empty($_GET['ldn_msg'])) {
        $msg = sanitize_text_field(wp_unslash($_GET['ldn_msg']));
        if ($msg === 'activated')   $notice = '<div style="color:#16a34a;margin:0 0 10px">Notification activée.</div>';
        if ($msg === 'deactivated') $notice = '<div style="color:#dbd0be;margin:0 0 10px">Notification désactivée.</div>';
        if ($msg === 'error')       $notice = '<div style="color:#b91c1c;margin:0 0 10px">Erreur lors de la mise à jour.</div>';
      }

      // Query
      $q = new WP_Query([
        'post_type'      => $this->cpt,
        'post_status'    => ['publish','draft','pending','future','private'],
        'posts_per_page' => (int)$a['per_page'],
        'orderby'        => 'title',
        'order'          => 'ASC',
        'no_found_rows'  => true,
      ]);

      ob_start();
      ?>
      <div class="ldn-wrap" style="max-width:1100px;margin:0 auto">
        <h3 style="margin-top:0">✉️ Notifications E-city learn</h3>
        <?php echo $notice; ?>

        <?php if (!$q->have_posts()): ?>
          <p>Aucune notification trouvée.</p>
        <?php else: ?>
          <div style="overflow:auto">
            <table class="wp-list-table widefat fixed striped" style="min-width:820px">
              <thead>
                <tr>
                  <th style="width:60px">ID</th>
                  <th>Titre</th>
                  <th style="width:140px">Statut</th>
                  <th style="width:160px">Action</th>
                </tr>
              </thead>
              <tbody>
              <?php
              while ($q->have_posts()): $q->the_post();
                $pid = get_the_ID();
                $status = get_post_status($pid);
                $is_active = ($status === 'publish');
                $to_status = $is_active ? 'draft' : 'publish';
                $btn_label = $is_active ? 'Désactiver' : 'Activer';
                $btn_style = $is_active ? 'background:#dbd0be;border-color:#dbd0be;' : 'background:#10b981;border-color:#10b981;';

                // URL action via admin-ajax
                $endpoint = add_query_arg('action', 'ldn_toggle', admin_url('admin-ajax.php'));
                $action_url = add_query_arg([
                  'ldn_id'    => (int)$pid,
                  'ldn_to'    => $to_status,
                  'ldn_nonce' => wp_create_nonce('ldn_toggle_'.$pid),
                  'ldn_back'  => rawurlencode($back_url), // éviter double-encodages
                ], $endpoint);
              ?>
                <tr>
                  <td><?php echo (int)$pid; ?></td>
                  <td><?php echo esc_html(get_the_title()); ?></td>
                  <td><?php echo $is_active ? '<span style="color:#059669;font-weight:600">Activée</span>' : '<span style="color:#6b7280">Désactivée</span>'; ?></td>
                  <td>
                    <a class="button button-primary" style="<?php echo esc_attr($btn_style); ?>"
                       href="<?php echo esc_url($action_url); ?>">
                       <?php echo esc_html($btn_label); ?>
                    </a>
                  </td>
                </tr>
              <?php endwhile; wp_reset_postdata(); ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
      <?php
      return ob_get_clean();
    }

    /** Handler AJAX */
    public function ajax_toggle(){
      // Rien n'est envoyé avant cette ligne (évite "headers already sent")
      if (!$this->can_manage()) {
        wp_die('Accès refusé.', 403);
      }

      $pid  = isset($_GET['ldn_id']) ? absint($_GET['ldn_id']) : 0;
      $to   = isset($_GET['ldn_to']) ? sanitize_key($_GET['ldn_to']) : '';
      $back_raw = isset($_GET['ldn_back']) ? wp_unslash($_GET['ldn_back']) : '';
      $back = $back_raw ? esc_url_raw(rawurldecode($back_raw)) : '';

      if (empty($back)) {
        $ref = wp_get_referer();
        $back = $ref ? $ref : home_url('/');
      }

      if (!$pid || !in_array($to, ['publish','draft'], true)) {
        wp_safe_redirect($back); exit;
      }
      $nonce = isset($_GET['ldn_nonce']) ? $_GET['ldn_nonce'] : '';
      if (!wp_verify_nonce($nonce, 'ldn_toggle_'.$pid)) {
        wp_safe_redirect($back); exit;
      }

      // Vérification finale (passe par map_meta_cap)
      if (!current_user_can('edit_post', $pid)) {
        wp_safe_redirect($back); exit;
      }

      $res = wp_update_post([
        'ID'          => $pid,
        'post_status' => $to,
      ], true);

      $msg  = (is_wp_error($res)) ? 'error' : (($to === 'publish') ? 'activated' : 'deactivated');
      $back = add_query_arg('ldn_msg', $msg, $back);

      wp_safe_redirect($back);
      exit;
    }
  }

  // Boot
  LDN_Notify_Manager::instance();
}


// === Dashboard ===
/* ========= DASHBOARD KPI XXL : [lms_stats] ========= */
add_shortcode('lms_stats', function($atts = []){

  // ---------- Options ----------
  $a = shortcode_atts([
    'refresh'    => '0',          // "1" pour ignorer le cache
    'enrol_mode' => 'access',     // "access" (accès au cours) ou "enrolled" (strict)
  ], $atts, 'lms_stats');

  $bust_cache = $a['refresh'] === '1';
  $enrol_mode = ($a['enrol_mode'] === 'enrolled') ? 'enrolled' : 'access';

  // ---------- Cache (5 min) ----------
  $cache_key = 'lms_stats_cache_' . md5($enrol_mode);
  if (!$bust_cache) {
    $cached = get_transient($cache_key);
    if (is_array($cached)) {
      return lms_stats_render($cached);
    }
  }

  // ---------- Helpers ----------
  if (!function_exists('lms_count_published_posts')) {
    function lms_count_published_posts($post_type){
      // Essai rapide via wp_count_posts en mode "readable"
      $_obj = wp_count_posts($post_type, 'readable');
      $count = (isset($_obj->publish)) ? (int) $_obj->publish : 0;

      // Fallback si 0 → compter précisément avec WP_Query (found_posts)
      if ($count === 0) {
        $q = new WP_Query([
          'post_type'              => $post_type,
          'post_status'            => 'publish',
          'posts_per_page'         => 1,
          'paged'                  => 1,
          'fields'                 => 'ids',
          'no_found_rows'          => false, // nécessaire pour found_posts
          'update_post_meta_cache' => false,
          'update_post_term_cache' => false,
        ]);
        $count = (int) $q->found_posts;
        wp_reset_postdata();
      }
      return $count;
    }
  }

  if (!function_exists('lms_count_groups')) {
    function lms_count_groups(){
      if (!post_type_exists('groups')) return 0;
      return lms_count_published_posts('groups');
    }
  }

  if (!function_exists('lms_count_non_admin_users')) {
    function lms_count_non_admin_users(){
      // Compte tous les utilisateurs EXCEPTÉ administrateurs
      $uq = new WP_User_Query([
        'role__not_in'  => ['administrator'],
        'number'        => 1,       // on ne ramène pas toute la liste
        'fields'        => 'ID',
        'count_total'   => true,    // important pour obtenir total
      ]);
      // total est dans $uq->get_total() sur WP >= 5.x
      $total = (int) $uq->get_total();
      // fallback si besoin
      if (!$total) {
        $counts = count_users();
        $sum = 0;
        if (!empty($counts['avail_roles'])) {
          foreach ($counts['avail_roles'] as $role => $n) {
            if ($role === 'administrator') continue;
            $sum += (int)$n;
          }
        }
        $total = $sum;
      }
      return $total;
    }
  }

  if (!function_exists('lms_count_enrolments')) {
    function lms_count_enrolments($mode = 'access'){
      $total = 0;

      if ($mode === 'enrolled') {
        // Somme pour chaque utilisateur du nombre de cours où il est "inscrit"
        $user_ids = get_users(['fields' => 'ID']);
        foreach ($user_ids as $uid) {
          if (function_exists('learndash_user_get_enrolled_courses')) {
            $enrolled = learndash_user_get_enrolled_courses($uid, ['num' => -1, 'return' => 'ids']);
            if (is_array($enrolled)) $total += count($enrolled);
          }
        }
        return $total;
      }

      // mode "access" (recommandé) : nb d’utilisateurs ayant accès à chaque cours (inscription, groupe, open, achat…)
      $course_ids = get_posts([
        'post_type'   => 'sfwd-courses',
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields'      => 'ids',
      ]);

      foreach ($course_ids as $course_id) {
        $count_for_course = 0;

        if (function_exists('learndash_get_users_for_course')) {
          $uq = learndash_get_users_for_course($course_id, [
            'number'        => 1,     // on ne liste pas, on veut juste le total
            'fields'        => 'ID',
            'having_access' => true,  // accès via inscription, groupe, open…
          ]);

          if ($uq instanceof WP_User_Query) {
            $count_for_course = (int) $uq->get_total();
          } elseif (is_array($uq) && isset($uq['total_users'])) {
            $count_for_course = (int) $uq['total_users'];
          }
        }

        // Fallback si l’API ci-dessus n’est pas dispo
        if ($count_for_course === 0 && function_exists('ld_course_access_list')) {
          $access = ld_course_access_list($course_id);
          if (is_string($access)) {
            $ids = array_filter(array_map('intval', explode(',', $access)));
            $count_for_course = count($ids);
          } elseif (is_array($access)) {
            $count_for_course = count($access);
          }
        }

        $total += $count_for_course;
      }

      return $total;
    }
  }

  // ---------- Collecte des données ----------
  $courses_count = lms_count_published_posts('sfwd-courses');
  $lessons       = lms_count_published_posts('sfwd-lessons');
  $topics        = lms_count_published_posts('sfwd-topic');
  $quizzes       = lms_count_published_posts('sfwd-quiz');
  $groups        = lms_count_groups();
  $users_total   = lms_count_non_admin_users();
  $enrol_total   = lms_count_enrolments($enrol_mode);

  global $wpdb;
  $total_seconds = (int) $wpdb->get_var(
    $wpdb->prepare(
      "SELECT SUM(CAST(meta_value AS UNSIGNED)) 
       FROM {$wpdb->usermeta} WHERE meta_key=%s", 'ld_total_seconds'
    )
  );
  $hours = number_format(max(0,$total_seconds)/3600, 1, ',', ' ');

  $data = [
    ['label'=>'Parcours',     'value'=>$courses_count,  'icon'=>'📚', 'tone'=>1],
    ['label'=>'Formations',   'value'=>$lessons,        'icon'=>'📘', 'tone'=>2],
    ['label'=>'Modules',      'value'=>$topics,         'icon'=>'🧩', 'tone'=>3],
    ['label'=>'Quiz',         'value'=>$quizzes,        'icon'=>'❓', 'tone'=>1],
    ['label'=>'Groupes',      'value'=>$groups,         'icon'=>'👥', 'tone'=>2],
    ['label'=>'Utilisateurs', 'value'=>$users_total,    'icon'=>'👤', 'tone'=>3],
    ['label'=>'Inscriptions', 'value'=>$enrol_total,    'icon'=>'✅', 'tone'=>1],
    ['label'=>'Heures',       'value'=>$hours.'<span> h</span>', 'icon'=>'⏱️','tone'=>2],
  ];

  // ---------- Cache ----------
  set_transient($cache_key, $data, 5 * MINUTE_IN_SECONDS);

  // ---------- Render ----------
  return lms_stats_render($data);
});

/* ====== RENDER UNIQUEMENT (mise en forme "clean KPIs") ====== */
if (!function_exists('lms_stats_render')) {
  function lms_stats_render($items){
    ob_start(); ?>
    <style>
      /* Styles SCOPÉS au composant uniquement */
      .dmm-skin-clean{ --kpi-primary:#2E3143; --kpi-muted:#9AA3AE; --kpi-div:rgba(46,49,67,.14); }
      .dmm-skin-clean .dmm-kpi-grid--xl{
        display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:0;
      }
      @media (max-width:1024px){ .dmm-skin-clean .dmm-kpi-grid--xl{ grid-template-columns:repeat(2, minmax(0,1fr)); } }
      @media (max-width:640px){  .dmm-skin-clean .dmm-kpi-grid--xl{ grid-template-columns:1fr; } }

      /* Carte KPI : on garde tout, on réorganise seulement */
      .dmm-skin-clean .dmm-kpi{
        position:relative; padding:26px 8px 20px;
        display:flex; flex-direction:column; align-items:center; text-align:center;
        background:transparent; border:none; box-shadow:none;
      }

      /* Icône & accent : présents dans le HTML mais masqués visuellement */
      .dmm-skin-clean .dmm-kpi-icon,
      .dmm-skin-clean .dmm-kpi-accent{ display:none !important; }

      /* Valeur au-dessus, XXL */
      .dmm-skin-clean .dmm-kpi-value{
        order:1; font-size:clamp(28px, 6vw, 56px); font-weight:800;
        line-height:1.05; letter-spacing:.02em; color:var(--kpi-primary);
      }
      .dmm-skin-clean .dmm-kpi-value span{ font-size:.5em; opacity:.7; }

      /* Libellé en dessous, gris */
      .dmm-skin-clean .dmm-kpi-label{
        order:2; margin-top:8px; font-size:15px; line-height:1.3;
        font-weight:500; color:var(--kpi-muted);
      }

      /* Séparateurs verticaux (désactivés sur mobile) */
      @media (min-width:641px){
        .dmm-skin-clean .dmm-kpi::after{
          content:""; position:absolute; right:0; top:22%; bottom:22%;
          width:1px; background:var(--kpi-div);
        }
      }
      /* Pas de séparateur en fin de ligne (3 colonnes desktop) */
      @media (min-width:1025px){ .dmm-skin-clean .dmm-kpi:nth-child(3n)::after{ display:none; } }
      /* Pas de séparateur en fin de ligne (2 colonnes tablette) */
      @media (min-width:641px) and (max-width:1024px){ .dmm-skin-clean .dmm-kpi:nth-child(2n)::after{ display:none; } }
    </style>

    <section class="dmm-dash dmm-dash--xl dmm-skin-clean" aria-label="Statistiques">
      <div class="dmm-kpi-grid dmm-kpi-grid--xl">
        <?php foreach ($items as $it): ?>
          <div class="dmm-kpi dmm-kpi--tone<?php echo (int)$it['tone']; ?>">
            <div class="dmm-kpi-icon"><?php echo esc_html($it['icon']); ?></div>
            <div class="dmm-kpi-label"><?php echo esc_html($it['label']); ?></div>
            <div class="dmm-kpi-value"><?php echo wp_kses_post($it['value']); ?></div>
            <div class="dmm-kpi-accent"></div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <?php
    return ob_get_clean();
  }
}


// === lmsmanagegroup ===
/**
 * Donner au rôle LMS Manager l'accès aux shortcodes [uo_groups] et [uo_groups_create_group]
 */
add_action('init', function(){
    $role = get_role('lms_manager');
    if ($role) {
        // capability utilisée par Uncanny Groups
        $role->add_cap('group_leader');
    }
});

// === lmsadmin = instructeur ===

/**
 * Auto-ajout du rôle wdm_instructor pour tous les lms_admin
 * Place ce code dans functions.php ou crée un plugin
 */

// 1) À la connexion
add_action('wp_login', function($user_login, $user) {
    if (in_array('lms_admin', (array) $user->roles)) {
        if (!in_array('wdm_instructor', (array) $user->roles)) {
            $user->add_role('wdm_instructor');
            update_user_meta($user->ID, 'ir_is_instructor', 'yes');
            update_user_meta($user->ID, 'wdm_instructor_role', true);
        }
    }
}, 10, 2);

// 2) Quand un utilisateur devient lms_admin
add_action('set_user_role', function($user_id, $role, $old_roles) {
    if ($role === 'lms_admin') {
        $user = get_userdata($user_id);
        if (!in_array('wdm_instructor', (array) $user->roles)) {
            $user->add_role('wdm_instructor');
            update_user_meta($user_id, 'ir_is_instructor', 'yes');
            update_user_meta($user_id, 'wdm_instructor_role', true);
        }
    }
}, 10, 3);

// 3) Quand un rôle est ajouté à un utilisateur existant
add_action('add_user_role', function($user_id, $role) {
    if ($role === 'lms_admin') {
        $user = get_userdata($user_id);
        if (!in_array('wdm_instructor', (array) $user->roles)) {
            $user->add_role('wdm_instructor');
            update_user_meta($user_id, 'ir_is_instructor', 'yes');
            update_user_meta($user_id, 'wdm_instructor_role', true);
        }
    }
}, 10, 2);

// 4) À chaque chargement de page (sécurité)
add_action('init', function() {
    if (!is_user_logged_in()) return;
    
    $user = wp_get_current_user();
    if (in_array('lms_admin', (array) $user->roles)) {
        if (!in_array('wdm_instructor', (array) $user->roles)) {
            $user->add_role('wdm_instructor');
            update_user_meta($user->ID, 'ir_is_instructor', 'yes');
            update_user_meta($user->ID, 'wdm_instructor_role', true);
        }
    }
}, 999);

// 5) Fonction pour corriger tous les utilisateurs existants (exécute une seule fois)
function lms_admin_add_instructor_to_all() {
    $users = get_users(array('role' => 'lms_admin'));
    $count = 0;
    
    foreach ($users as $user) {
        if (!in_array('wdm_instructor', (array) $user->roles)) {
            $user->add_role('wdm_instructor');
            update_user_meta($user->ID, 'ir_is_instructor', 'yes');
            update_user_meta($user->ID, 'wdm_instructor_role', true);
            $count++;
        }
    }
    
    return $count;
}

// 6) Exécuter automatiquement une fois à l'activation
add_action('admin_init', function() {
    // Vérifie si déjà exécuté
    if (get_option('lms_admin_instructor_sync_done')) {
        return;
    }
    
    // Exécuter la synchronisation
    $count = lms_admin_add_instructor_to_all();
    
    // Marquer comme fait
    update_option('lms_admin_instructor_sync_done', true);
    
    // Message admin (optionnel)
    add_action('admin_notices', function() use ($count) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>✅ Synchronisation terminée :</strong> ' . $count . ' utilisateur(s) lms_admin ont reçu le rôle wdm_instructor.</p>';
        echo '</div>';
    });
}, 1);

// === administrateur LMS ===
/**
 * Rôle "Administrateur LMS" : mêmes capacités qu'Administrator (pour le front),
 * avec accès à la création de cours LearnDash.
 *
 * - Crée/MAJ le rôle `lms_admin`
 * - Copie TOUTES les capabilities du rôle `administrator`
 * - Ajoute 'group_leader' + 'wdm_instructor' (pour dashboard instructeur)
 * - Bloque l'accès à /wp-admin/ SAUF dashboard instructeur + création de cours
 * - Cache la barre d'admin sur le front
 * - Autorise `lms_admin` pour [ld_user_admin] et [ld_notify_manager]
 */

/* 1) Créer/mettre à jour le rôle et lui donner les caps admin */
add_action('init', function () {
  // Crée le rôle s'il n'existe pas
  $role = get_role('lms_admin');
  if (!$role) {
    $role = add_role('lms_admin', 'Administrateur LMS', ['read' => true]);
  }
  
  // Copie toutes les caps d'Administrator
  $admin = get_role('administrator');
  if ($role && $admin) {
    foreach ((array) $admin->capabilities as $cap => $grant) {
      if ($grant) $role->add_cap($cap);
    }
    // Ajouter les caps nécessaires pour le dashboard instructeur
    $role->add_cap('group_leader');
    $role->add_cap('wdm_instructor');
    $role->add_cap('instructor_page');
    $role->add_cap('instructor_reports');
    
    // Capacités pour la création/édition de cours LearnDash
    $role->add_cap('edit_courses');
    $role->add_cap('edit_course');
    $role->add_cap('publish_courses');
    $role->add_cap('delete_courses');
    $role->add_cap('edit_published_courses');
    $role->add_cap('delete_published_courses');
    $role->add_cap('edit_others_courses');
    $role->add_cap('delete_others_courses');
    $role->add_cap('read_course');
    
    // Capacités pour les leçons
    $role->add_cap('edit_lessons');
    $role->add_cap('edit_lesson');
    $role->add_cap('publish_lessons');
    $role->add_cap('delete_lessons');
    
    // Capacités pour les topics
    $role->add_cap('edit_topics');
    $role->add_cap('edit_topic');
    $role->add_cap('publish_topics');
    
    // Capacités pour les quiz
    $role->add_cap('edit_quizzes');
    $role->add_cap('edit_quiz');
    $role->add_cap('publish_quizzes');
  }
  
  // Ajouter aussi le rôle wdm_instructor aux utilisateurs lms_admin
  $current_user = wp_get_current_user();
  if ($current_user && $current_user->ID > 0 && in_array('lms_admin', (array) $current_user->roles)) {
    if (!in_array('wdm_instructor', (array) $current_user->roles)) {
      $current_user->add_role('wdm_instructor');
    }
    // Métadonnées instructeur
    update_user_meta($current_user->ID, 'ir_is_instructor', 'yes');
    update_user_meta($current_user->ID, 'wdm_instructor_role', true);
  }
});

/* 2) Bloquer l'accès au back-office pour lms_admin SAUF dashboard instructeur + création de cours */
add_action('admin_init', function () {
  if (!is_user_logged_in()) return;
  
  $u = wp_get_current_user();
  if (!in_array('lms_admin', (array) $u->roles, true)) {
    return; // Pas un lms_admin, ne rien faire
  }
  
  // Laisse passer AJAX/REST
  if ((defined('DOING_AJAX') && DOING_AJAX) || (defined('REST_REQUEST') && REST_REQUEST)) {
    return;
  }
  
  // ✅ AUTORISER l'accès à TOUTES les pages du dashboard instructeur
  $current_page = isset($_GET['page']) ? $_GET['page'] : '';
  $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
  $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
  $action = isset($_GET['action']) ? $_GET['action'] : '';
  $post = isset($_GET['post']) ? $_GET['post'] : '';
  
  // Pages autorisées pour lms_admin
  $allowed_conditions = array(
    // Pages du dashboard instructeur
    strpos($current_page, 'ir_') === 0,
    strpos($current_page, 'instructor') !== false,
    strpos($request_uri, 'ir_instructor') !== false,
    strpos($request_uri, 'instructor-role') !== false,
    
    // Post types LearnDash (courses, lessons, topics, quiz)
    $post_type === 'sfwd-courses',
    $post_type === 'sfwd-lessons',
    $post_type === 'sfwd-topic',
    $post_type === 'sfwd-quiz',
    $post_type === 'sfwd-question',
    
    // Édition de posts LearnDash
    ($action === 'edit' && $post),
    
    // Pages d'édition directes
    strpos($request_uri, 'post.php') !== false,
    strpos($request_uri, 'post-new.php') !== false,
    
    // Admin AJAX pour les builders
    strpos($request_uri, 'admin-ajax.php') !== false,
    
    // Media library (pour les images de cours)
    strpos($request_uri, 'upload.php') !== false,
    strpos($request_uri, 'media-upload.php') !== false,
    $current_page === 'upload',
    
    // Async upload (pour l'upload d'images)
    strpos($request_uri, 'async-upload.php') !== false,
  );
  
  // Si au moins une condition est vraie, autoriser l'accès
  foreach ($allowed_conditions as $condition) {
    if ($condition) {
      return; // AUTORISER
    }
  }
  
  // Vérifier aussi si on édite un post de type LearnDash
  if (is_admin() && function_exists('get_current_screen')) {
    $screen = get_current_screen();
    if ($screen) {
      $allowed_screens = array(
        'sfwd-courses',
        'sfwd-lessons', 
        'sfwd-topic',
        'sfwd-quiz',
        'sfwd-question',
        'edit-sfwd-courses',
        'edit-sfwd-lessons',
        'edit-sfwd-topic',
        'edit-sfwd-quiz',
      );
      
      if (in_array($screen->id, $allowed_screens) || 
          in_array($screen->post_type, $allowed_screens)) {
        return; // AUTORISER
      }
      
      // Autoriser aussi si le screen contient "instructor"
      if (strpos($screen->id, 'instructor') !== false || 
          strpos($screen->id, 'ir_') !== false) {
        return; // AUTORISER
      }
    }
  }
  
  // ❌ Bloquer tout le reste de wp-admin
  if (is_admin()) {
    wp_safe_redirect(home_url('/'));
    exit;
  }
});

/* 3) Cacher la barre d'admin sur le front pour lms_admin */
add_filter('show_admin_bar', function ($show) {
  if (!is_user_logged_in()) return $show;
  $u = wp_get_current_user();
  if (in_array('lms_admin', (array) $u->roles, true)) {
    // Montrer la barre admin UNIQUEMENT si on est dans wp-admin
    if (is_admin()) {
      return true;
    }
    return false; // Cacher sur le front
  }
  return $show;
});

/* 4) Autoriser `lms_admin` pour les shortcodes front personnalisés */
add_filter('ld_user_admin_allowed_roles', function ($roles) {
  $roles[] = 'lms_admin';
  return array_values(array_unique($roles));
});

add_filter('ldn_allowed_roles', function ($roles) {
  $roles[] = 'lms_admin';
  return array_values(array_unique($roles));
});

/* 5) S'assurer que wdm_instructor est toujours premier dans l'ordre des rôles */
add_action('wp_login', function ($user_login, $user) {
  if (in_array('lms_admin', (array) $user->roles)) {
    // Ajouter wdm_instructor si manquant
    if (!in_array('wdm_instructor', (array) $user->roles)) {
      $user->add_role('wdm_instructor');
    }
    
    // Réorganiser les rôles : wdm_instructor en PREMIER
    $roles = $user->roles;
    
    // Retirer wdm_instructor de sa position
    $key = array_search('wdm_instructor', $roles);
    if ($key !== false) {
      unset($roles[$key]);
    }
    
    // Supprimer tous les rôles
    foreach ($user->roles as $role) {
      $user->remove_role($role);
    }
    
    // Réajouter dans le bon ordre : wdm_instructor EN PREMIER
    $user->add_role('wdm_instructor');
    foreach ($roles as $role) {
      if ($role !== 'wdm_instructor') {
        $user->add_role($role);
      }
    }
    
    // Métadonnées
    update_user_meta($user->ID, 'ir_is_instructor', 'yes');
    update_user_meta($user->ID, 'wdm_instructor_role', true);
    
    // Nettoyer le cache
    wp_cache_delete($user->ID, 'users');
  }
}, 10, 2);

/* 6) Forcer wdm_instructor en premier à chaque chargement de page */
add_action('init', function () {
  if (!is_user_logged_in()) return;
  
  $user = wp_get_current_user();
  if (!$user || $user->ID == 0) return;
  
  if (in_array('lms_admin', (array) $user->roles)) {
    // Vérifier si wdm_instructor est le premier rôle
    if (!isset($user->roles[0]) || $user->roles[0] !== 'wdm_instructor') {
      // Réorganiser
      $roles = $user->roles;
      
      // Retirer wdm_instructor
      $key = array_search('wdm_instructor', $roles);
      if ($key !== false) {
        unset($roles[$key]);
      }
      
      // Supprimer tous
      foreach ($user->roles as $role) {
        $user->remove_role($role);
      }
      
      // Réajouter dans le bon ordre
      $user->add_role('wdm_instructor');
      foreach ($roles as $role) {
        if ($role !== 'wdm_instructor') {
          $user->add_role($role);
        }
      }
      
      // Nettoyer le cache
      wp_cache_delete($user->ID, 'users');
    }
  }
}, 999);

/* 7) Debug : Afficher les infos sur les pages autorisées (OPTIONNEL - à retirer en prod) */
/*
add_action('admin_notices', function() {
  if (!current_user_can('manage_options')) return;
  
  $user = wp_get_current_user();
  $current_page = isset($_GET['page']) ? $_GET['page'] : '';
  $post_type = isset($_GET['post_type']) ? $_GET['post_type'] : '';
  
  echo '<div class="notice notice-info">';
  echo '<p><strong>DEBUG LMS Admin :</strong></p>';
  echo '<p>Rôles : ' . implode(', ', $user->roles) . '</p>';
  echo '<p>Premier rôle : ' . (isset($user->roles[0]) ? $user->roles[0] : 'aucun') . '</p>';
  echo '<p>Page actuelle : ' . $current_page . '</p>';
  echo '<p>Post type : ' . $post_type . '</p>';
  echo '</div>';
});
*/

// === lmsadmin groupleader ===

// 1) Quand on crée un utilisateur
add_action('user_register', function($user_id){
  $u = new WP_User($user_id);
  if (in_array('lms_admin', (array)$u->roles, true) && !in_array('group_leader', (array)$u->roles, true)) {
    $u->add_role('group_leader');
  }
});

// 2) Quand on change le rôle principal vers lms_admin
add_action('set_user_role', function($user_id, $role, $old_roles){
  if ($role === 'lms_admin') {
    $u = new WP_User($user_id);
    if (!in_array('group_leader', (array)$u->roles, true)) {
      $u->add_role('group_leader');
    }
  }
}, 10, 3);



// === LMS HUBADMIN RESTR ===
/**
 * Restreindre l'accès à Hub Admin (et toutes ses pages enfants)
 * Autorisés : administrator, lms_admin
 * Remplace le slug si besoin (par défaut: hub-admin)
 */
add_action('template_redirect', function () {
  if ( !is_page() ) return;

  $hub_slug = 'hub-admin'; // <== mets ici le slug EXACT de ta page parent Hub
  $hub_page = get_page_by_path($hub_slug, OBJECT, 'page');
  if ( !$hub_page ) return; // rien à faire si la page n'existe pas

  global $post;
  if ( !$post ) return;

  // Est-ce la page Hub elle-même ou une page descendante ?
  $ancestors = (array) get_post_ancestors($post->ID);
  $is_hub_area = ($post->ID === (int) $hub_page->ID) || in_array((int)$hub_page->ID, $ancestors, true);

  if ( !$is_hub_area ) return;

  // Vérifier rôles autorisés
  if ( !is_user_logged_in() ) {
    // envoyer vers login puis revenir sur la page demandée
    auth_redirect();
  }

  $user = wp_get_current_user();
  $allowed = array_intersect(['administrator','lms_admin'], (array) $user->roles);
  if ( empty($allowed) ) {
    // pas autorisé → page d'accueil (ou autre)
    wp_safe_redirect( home_url('/') );
    exit;
  }
});

// === translate ===
/**
 * Ajoute FR/EN au menu principal
 */
add_filter('wp_nav_menu_items', function($items, $args){
    // Remplace "primary" par l'emplacement de ton menu si différent
    if ($args->theme_location === 'primary') {
        $lang_switch  = '<li class="menu-item menu-item-language">';
        $lang_switch .= '<a href="?lang=fr">FR</a> | <a href="?lang=en">EN</a>';
        $lang_switch .= '</li>';
        $items .= $lang_switch;
    }
    return $items;
}, 10, 2);

/**
 * Active la langue LearnDash/WordPress selon ?lang=fr ou ?lang=en
 */
add_action('init', function(){
    if ( isset($_GET['lang']) ) {
        $locale = ($_GET['lang'] === 'en') ? 'en_US' : 'fr_FR';
        switch_to_locale($locale);
        setcookie('site_lang', $locale, time()+3600*24*30, '/');
    } elseif ( isset($_COOKIE['site_lang']) ) {
        switch_to_locale($_COOKIE['site_lang']);
    }
});


// === Force login ===
function ayoub_redirect_frontpage_for_guests() {
    if ( is_front_page() && ! is_user_logged_in() ) {
        wp_safe_redirect( home_url('/connexion/') );
        exit;
    }
}
add_action('template_redirect', 'ayoub_redirect_frontpage_for_guests');

// === DATALAB V2 ===
/** Helper: s'assurer qu'une activité "course" existe SANS démarrer le cours (pas commencé) */
function dl_ensure_course_activity($user_id, $course_id){
  if (!$user_id || !$course_id) return false;

  global $wpdb;
  $ua = $wpdb->prefix.'learndash_user_activity';

  // existe déjà ?
  $exists = $wpdb->get_var($wpdb->prepare(
    "SELECT activity_id FROM $ua
     WHERE user_id=%d AND course_id=%d AND post_id=%d AND activity_type='course' LIMIT 1",
    (int)$user_id, (int)$course_id, (int)$course_id
  ));
  if ($exists) return false;

  // CRÉER comme "PAS COMMENCÉ" : started=0, status=0
  $wpdb->insert(
    $ua,
    [
      'user_id'          => (int)$user_id,
      'course_id'        => (int)$course_id,
      'post_id'          => (int)$course_id,
      'activity_type'    => 'course',
      'activity_status'  => 0,
      'activity_started' => 0,                          // clé : PAS COMMENCÉ
      'activity_completed'=> 0,
      'activity_updated' => current_time('mysql', true) // ok de dater la création
    ],
    ['%d','%d','%d','%s','%d','%d','%d','%s']
  );
  return true;
}

/** Premier accès à la page d'un parcours → passer de "pas commencé" à "en cours" */
add_action('template_redirect', function(){
  if (!is_user_logged_in() || !is_singular('sfwd-courses')) return;

  $course_id = get_queried_object_id();
  $user_id   = get_current_user_id();

  if (!function_exists('sfwd_lms_has_access') || !sfwd_lms_has_access($course_id, $user_id)) return;

  global $wpdb;
  $ua = $wpdb->prefix.'learndash_user_activity';

  // Si la ligne existe et activity_started=0 → on démarre
  $wpdb->query($wpdb->prepare(
    "UPDATE $ua
     SET activity_started=%d, activity_updated=%s
     WHERE user_id=%d AND course_id=%d AND post_id=%d
       AND activity_type='course'
       AND (activity_started=0 OR activity_started IS NULL)
     LIMIT 1",
    time(), current_time('mysql', true), (int)$user_id, (int)$course_id, (int)$course_id
  ));
});

// === DATALAB V2.2 ===


// === Groupshortcode ===
/* Shortcode [uo_groups_switch] — onglets GESTION / CRÉATION
   ➕ Intègre en tête de l’onglet GESTION le tableau [ld_group_admin]
   Attributs :
   - manage       : shortcode de gestion (défaut: [uo_groups])
   - create       : shortcode de création (défaut: [uo_groups_create_group])
   - manage_label : libellé onglet gestion
   - create_label : libellé onglet création
   - default      : 'manage' ou 'create' (onglet par défaut)
   - show_table   : 'yes'/'no' pour afficher le tableau au-dessus de [uo_groups]
   - table_sc     : shortcode du tableau (défaut: [ld_group_admin per_page="20" search="yes"])
*/
add_shortcode('uo_groups_switch', function($atts){
  $a = shortcode_atts([
    'manage'       => '[uo_groups]',
    'create'       => '[uo_groups_create_group parent_selector="show"]',
    'manage_label' => 'ADMINISTRATION DES GROUPES',
    'create_label' => 'NOUVEAU GROUPE',
    'default'      => 'manage',
    'show_table'   => 'yes',
    'table_sc'     => '[ld_group_admin per_page="20" search="yes"]',
  ], $atts, 'uo_groups_switch');

  // Peut-il voir le tableau d’admin groupes ?
  $can_table = false;
  if (is_user_logged_in()) {
    $u = wp_get_current_user();
    $can_table = (bool) array_intersect(['administrator','lms_admin'], (array)$u->roles);
  }

  $uid = 'uogs_'.wp_generate_uuid4();
  $manage_active = ($a['default'] === 'manage');

  ob_start(); ?>
  <div id="<?php echo esc_attr($uid); ?>" class="uo-groups-switch" style="display:block;width:100%;">
    <!-- Onglets -->
    <div class="uo-gs-tabs" style="display:flex;gap:.5rem;align-items:center;margin-bottom:1rem;">
      <button type="button" data-target="manage"
        style="padding:.5rem .9rem;border:1px solid #ddd;border-radius:.5rem;cursor:pointer;<?php echo $manage_active?'font-weight:700;border-color:#bbb;':''; ?>">
        <?php echo esc_html($a['manage_label']); ?>
      </button>
      <button type="button" data-target="create"
        style="padding:.5rem .9rem;border:1px solid #ddd;border-radius:.5rem;cursor:pointer;<?php echo !$manage_active?'font-weight:700;border-color:#bbb;':''; ?>">
        <?php echo esc_html($a['create_label']); ?>
      </button>
    </div>

    <!-- Panneau GESTION -->
    <div class="uo-gs-panel uo-gs-manage" style="<?php echo $manage_active?'':'display:none;'; ?>">
      <?php
      // Bloc tableau d’admin groupes AVANT l’interface Uncanny (facultatif)
      if (strtolower($a['show_table']) === 'yes' && $can_table) {
        echo '<div class="uogs-admin-table" style="margin:0 0 1.2rem 0">';
        echo do_shortcode($a['table_sc']);          // <— [ld_group_admin …]
        echo '</div>';
      }
      // Contenu Uncanny – gestion des groupes
      echo do_shortcode($a['manage']);              // <— [uo_groups]
      ?>
    </div>

    <!-- Panneau CRÉATION -->
    <div class="uo-gs-panel uo-gs-create" style="<?php echo $manage_active?'display:none;':''; ?>">
      <?php echo do_shortcode($a['create']); // <— [uo_groups_create_group] ?>
    </div>
  </div>

<script>
(function(){
  const root = document.getElementById('<?php echo esc_js($uid); ?>');
  if(!root) return;

  const btns = root.querySelectorAll('.uo-gs-tabs button');
  const panelManage = root.querySelector('.uo-gs-manage');
  const panelCreate = root.querySelector('.uo-gs-create');

  function openTab(target){
    if(target === 'manage'){
      panelManage.style.display = '';
      panelCreate.style.display = 'none';
    } else {
      panelManage.style.display = 'none';
      panelCreate.style.display = '';
    }
    btns.forEach(b=>b.style.fontWeight='400');
    const activeBtn = root.querySelector('.uo-gs-tabs button[data-target="'+target+'"]');
    if(activeBtn) activeBtn.style.fontWeight='700';
  }

  // ✅ Activer onglet selon hash (#manage / #create)
  try {
    const h = (window.location.hash || '').toLowerCase();
    if (h === '#create') openTab('create');
    else if (h === '#manage') openTab('manage');
  } catch(e){}

  btns.forEach(btn=>{
    btn.addEventListener('click', function(e){
      e.preventDefault();
      const target = this.getAttribute('data-target');
      openTab(target);
      try { window.location.hash = target; } catch(e){}
    });
  });

  // ✅ Fix: après "Groupe créé...", ré-afficher le formulaire sans quitter la page
  // (Uncanny garde souvent un état de succès via querystring)
  try {
    const GUARD = 'uogs_fix_once_<?php echo esc_js($uid); ?>';

    const hasForm = () => !!panelCreate.querySelector('form');

    const isSuccessView = () => {
      const t = (panelCreate.textContent || '').toLowerCase();
      return (
        t.includes('groupe créé') ||
        t.includes('créé avec succès') ||
        t.includes('group created') ||
        !!panelCreate.querySelector('.uo-groups-message.success, .uo-groups__notice--success, .uo-ulgm-message.success, .success')
      );
    };

    // Si succès + pas de form => on recharge la page proprement (sans ?...) et on revient sur #create
    if (isSuccessView() && !hasForm() && sessionStorage.getItem(GUARD) !== '1') {
      sessionStorage.setItem(GUARD, '1');

      // URL propre : même page, sans query, en restant sur l'onglet création
      const cleanUrl = window.location.origin + window.location.pathname + '#create';
      window.location.replace(cleanUrl);
      return;
    }

    // Si on a rechargé et que le form est revenu, on enlève le garde-fou
    if (hasForm()) {
      sessionStorage.removeItem(GUARD);
    }
  } catch(e){}
})();
</script>

  <?php
  return ob_get_clean();
});
/*
Plugin Name: LMS Admin ULGM Patch
Description: Permet au rôle lms_admin d'accéder au shortcode [uo_groups] comme un administrateur.
Version: 1.0
Author: Votre Nom
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Ajouter 'lms_admin' aux rôles autorisés pour le shortcode [uo_groups]
add_filter( 'uo_groups_shortcode_args', function( $args ) {
    if ( isset( $args['allowed_roles'] ) && is_array( $args['allowed_roles'] ) ) {
        // Ajouter 'lms_admin' si absent
        if ( ! in_array( 'lms_admin', $args['allowed_roles'], true ) ) {
            $args['allowed_roles'][] = 'lms_admin';
        }
    }
    return $args;
});

/* Injection fantôme : forcer le chargement des assets Uncanny si leurs shortcodes
   ne sont pas présents en clair dans la page où on utilise [uo_groups_switch]. */
add_filter('the_posts', function($posts){
  if (is_admin() || empty($posts)) return $posts;

  foreach ($posts as $i => $p) {
    if (empty($p->post_content)) continue;

    if (strpos($p->post_content, '[uo_groups_switch') !== false) {
      $has_manage = (strpos($p->post_content, '[uo_groups]') !== false);
      $has_create = (strpos($p->post_content, '[uo_groups_create_group]') !== false);
      if (!$has_manage || !$has_create) {
        $ghost  = "\n\n<!-- Ghost Uncanny shortcodes for asset enqueue -->\n";
        $ghost .= "<div class='uo-gs-ghost' style='display:none!important' aria-hidden='true' hidden>";
        if (!$has_manage) $ghost .= "[uo_groups]";
        if (!$has_create) $ghost .= "[uo_groups_create_group]";
        $ghost .= "</div>\n";
        $posts[$i]->post_content .= $ghost;
      }
    }
  }
  return $posts;
}, 5);

// === réglageheader ===

/**
 * Force CSP compatible Storyline/SCORM/Tin Canny (workers blob:)
 */
function my_csp_header_value() {
  $d = parse_url(home_url(), PHP_URL_HOST);
  return "default-src 'self' https: data: blob:; ".
         "script-src 'self' 'unsafe-inline' 'unsafe-eval' https: blob:; ".
         "worker-src 'self' blob:; ".
         "connect-src 'self' https: data: blob: *.$d; ".
         "img-src 'self' https: data: blob:; ".
         "style-src 'self' 'unsafe-inline' https:; ".
         "frame-src 'self' https: data: blob:; ".
         "media-src 'self' https: data: blob:; ".
         "frame-ancestors 'self';";
}

// Front & REST
add_action('send_headers', function() {
  header_remove('Content-Security-Policy');
  header('Content-Security-Policy: '.my_csp_header_value());
}, 0);

// admin-ajax.php (AJAX Tin Canny)
add_action('admin_init', function() {
  if (defined('DOING_AJAX') && DOING_AJAX) {
    header_remove('Content-Security-Policy');
    header('Content-Security-Policy: '.my_csp_header_value());
  }
}, 0);

// === Modif profil ===
/**
 * Nettoyage du profil BuddyPress / BuddyBoss
 * - Supprime Forums et LMS
 * - Supprime uniquement le sous-onglet "Modifier" du profil
 */
add_action('bp_setup_nav', function () {
    // Supprimer les onglets principaux inutiles
    bp_core_remove_nav_item('forums');
    bp_core_remove_nav_item('forum');
    bp_core_remove_nav_item('lms');
    bp_core_remove_nav_item('courses');
    bp_core_remove_nav_item('learndash');

    // Supprimer uniquement le sous-onglet "Modifier" du profil
    bp_core_remove_subnav_item('profile', 'edit');
}, 999);

// === statespaceprofil ===
/**
 * [ld_user_overview]
 * 4 compteurs sur une seule ligne (Formations complétées, Parcours restants, Parcours inscrits, Heures passées)
 * puis "Groupe : …"
 * Options : hours_decimals | group_link | group_sep | group_empty | size=md|lg|xl|xxl
 */
add_shortcode('ld_user_overview', function($atts){
  if (!is_user_logged_in()) return '';

  $a = shortcode_atts([
    'hours_decimals' => '1',
    'group_link'     => 'no',
    'group_sep'      => ' • ',
    'group_empty'    => 'Aucun groupe',
    'size'           => 'xl', // md | lg | xl | xxl
  ], $atts, 'ld_user_overview');

  // Map des tailles -> variables CSS
  $presets = [
    'md'  => ['--value-size'=>'40px','--label-size'=>'13px','--group-size'=>'13.5px','--gap-x'=>'32px','--gap-y'=>'18px','--sep-h'=>'48px','--minw'=>'140px','--wrap-max'=>'840px'],
    'lg'  => ['--value-size'=>'48px','--label-size'=>'14px','--group-size'=>'14px','--gap-x'=>'36px','--gap-y'=>'20px','--sep-h'=>'52px','--minw'=>'150px','--wrap-max'=>'880px'],
    'xl'  => ['--value-size'=>'56px','--label-size'=>'16px','--group-size'=>'16px','--gap-x'=>'44px','--gap-y'=>'24px','--sep-h'=>'56px','--minw'=>'170px','--wrap-max'=>'980px'],
    'xxl' => ['--value-size'=>'64px','--label-size'=>'18px','--group-size'=>'18px','--gap-x'=>'52px','--gap-y'=>'26px','--sep-h'=>'60px','--minw'=>'190px','--wrap-max'=>'1080px'],
  ];
  $p = $presets[ strtolower($a['size']) ] ?? $presets['xl'];

  // Données LearnDash
  $lessons   = trim(do_shortcode('[ld_stat type="lessons_completed"]'));
  $remaining = trim(do_shortcode('[ld_stat type="remaining"]'));
  $enrolled  = trim(do_shortcode('[ld_stat type="enrolled"]'));
  // (interactions supprimé)
  $hours     = trim(do_shortcode('[ld_hours decimals="'.esc_attr($a['hours_decimals']).'"]'));
  $groups    = do_shortcode('[ld_user_groups link="'.esc_attr($a['group_link']).'" sep="'.esc_attr($a['group_sep']).'" empty="'.esc_attr($a['group_empty']).'"]');

  $uid = 'ldovc_'.wp_generate_password(6,false,false);

  ob_start(); ?>
  <div id="<?php echo esc_attr($uid); ?>" class="ldovc ldovc--stack"
       style="--value-size:<?php echo esc_attr($p['--value-size']); ?>;
              --label-size:<?php echo esc_attr($p['--label-size']); ?>;
              --group-size:<?php echo esc_attr($p['--group-size']); ?>;
              --gap-x:<?php echo esc_attr($p['--gap-x']); ?>;
              --gap-y:<?php echo esc_attr($p['--gap-y']); ?>;
              --sep-h:<?php echo esc_attr($p['--sep-h']); ?>;
              --minw:<?php echo esc_attr($p['--minw']); ?>;
              --wrap-max:<?php echo esc_attr($p['--wrap-max']); ?>;">

    <!-- Ligne unique : 4 items -->
    <div class="ldovc-row ldovc-row--top">
      <div class="ldovc-item">
        <div class="ldovc-value"><?php echo esc_html($lessons); ?></div>
        <div class="ldovc-label">Formations complétées</div>
      </div>
      <div class="ldovc-sep" aria-hidden="true"></div>

      <div class="ldovc-item">
        <div class="ldovc-value"><?php echo esc_html($remaining); ?></div>
        <div class="ldovc-label">Parcours restants</div>
      </div>
      <div class="ldovc-sep" aria-hidden="true"></div>

      <div class="ldovc-item">
        <div class="ldovc-value"><?php echo esc_html($enrolled); ?></div>
        <div class="ldovc-label">Parcours inscrits</div>
      </div>
      <div class="ldovc-sep" aria-hidden="true"></div>

      <div class="ldovc-item">
        <div class="ldovc-value"><?php echo esc_html($hours); ?></div>
        <div class="ldovc-label">Heures passées</div>
      </div>
    </div>

    <!-- Groupes (inchangé) -->
    <div class="ldovc-groups">
      <strong>Groupe :</strong>
      <span class="ldovc-glist"><?php echo wp_kses_post($groups); ?></span>
    </div>
  </div>

  <style>
    #<?php echo esc_attr($uid); ?>.ldovc{
      --color: #0f172a; --muted: #6b7280; --sep: #e5e7eb;
      text-align:center; margin:0 auto; max-width: var(--wrap-max);
    }
    #<?php echo esc_attr($uid); ?> .ldovc-row{
      display:flex; align-items:center; justify-content:center;
      gap: var(--gap-x); flex-wrap:nowrap;
    }
    #<?php echo esc_attr($uid); ?> .ldovc-row--top{ margin-bottom: var(--gap-y); }

    #<?php echo esc_attr($uid); ?> .ldovc-item{
      min-width: var(--minw);
      display:flex; flex-direction:column; align-items:center; justify-content:center;
    }
    #<?php echo esc_attr($uid); ?> .ldovc-value{
      font-weight: 800; font-size: var(--value-size); line-height:1; color:var(--color);
      margin-bottom: 10px; letter-spacing:.2px;
    }
    #<?php echo esc_attr($uid); ?> .ldovc-label{
      font-size: var(--label-size); color: var(--muted); letter-spacing:.2px; white-space:nowrap;
    }
    #<?php echo esc_attr($uid); ?> .ldovc-sep{
      width:1px; height:var(--sep-h); background:var(--sep); align-self:center;
    }
    #<?php echo esc_attr($uid); ?> .ldovc-groups{
      margin-top: 8px; font-size: var(--group-size); color: var(--muted);
    }
    #<?php echo esc_attr($uid); ?> .ldovc-groups strong{ color: var(--color); margin-right:6px; font-weight:600; }

    @media (max-width: 900px){
      #<?php echo esc_attr($uid); ?> .ldovc-row{ gap: calc(var(--gap-x) - 10px); }
      #<?php echo esc_attr($uid); ?> .ldovc-item{ min-width: calc(var(--minw) - 20px); }
    }
    @media (max-width: 680px){
      #<?php echo esc_attr($uid); ?> .ldovc-sep{ display:none; }
      #<?php echo esc_attr($uid); ?> .ldovc-row{ flex-wrap:wrap; gap: 22px; }
      #<?php echo esc_attr($uid); ?> .ldovc-item{ min-width: 48%; }
    }
  </style>
  <?php
  return ob_get_clean();
});
// === restrict animateur ===

/**
 * Espace animateur : visible UNIQUEMENT aux Group Leaders.
 * Compatible MU-PLUGIN.
 */

add_action('init', function () {

  /* === Réglages === */
  if (!function_exists('anim_space_slug')) {
    function anim_space_slug() { return 'espace-animateur'; } // <-- CHANGE SI BESOIN
  }

  /* Rôles autorisés */
  if (!function_exists('anim_space_allowed_roles')) {
    function anim_space_allowed_roles() {
      return apply_filters('anim_space_allowed_roles', ['group_leader']);
      // Exemple pour ajouter lms_admin : ['group_leader','lms_admin']
    }
  }

  /* Helpers */
  if (!function_exists('anim_space_page_id')) {
    function anim_space_page_id() {
      static $pid = null;
      if ($pid !== null) return $pid;
      $p = get_page_by_path(anim_space_slug());
      $pid = $p ? (int) $p->ID : 0;
      return $pid;
    }
  }

  if (!function_exists('anim_space_user_is_allowed')) {
    function anim_space_user_is_allowed() {
      if (!is_user_logged_in()) return false;
      $u = wp_get_current_user();
      return (bool) array_intersect(anim_space_allowed_roles(), (array) $u->roles);
    }
  }

  /* 1) Blocage d'accès à la page (front) */
  add_action('template_redirect', function () {
    $pid = anim_space_page_id();
    if ($pid && is_page($pid)) {
      if (!is_user_logged_in()) {
        wp_safe_redirect( wp_login_url( get_permalink($pid) ) );
        exit;
      }
      if (!anim_space_user_is_allowed()) {
        wp_safe_redirect( home_url('/') );
        exit;
      }
    }
  });

  /* 2) Retirer l’entrée de menu “Espace animateur” pour les non-autorisés */
  add_filter('wp_nav_menu_objects', function ($items, $args) {
    $pid = anim_space_page_id();
    if (!$pid) return $items;
    if (anim_space_user_is_allowed()) return $items;

    foreach ($items as $i => $item) {
      if ((int) $item->object_id === $pid) {
        unset($items[$i]);
      }
    }
    return array_values($items);
  }, 10, 2);

  /* 3) Shortcode de confort pour conditionner du contenu */
  add_shortcode('if_group_leader', function ($atts, $content = '') {
    return anim_space_user_is_allowed() ? do_shortcode($content) : '';
  });

}, 20); // <-- priorité 20 : garantit que tout WP est prêt

// === Codesuppgroupe ===
/**
 * [ld_group_admin] — Liste des groupes LearnDash (publiés) + suppression via AJAX (front)
 * Accès : administrator + lms_admin
 * V3.0 — Fix définitif : delete via admin-ajax (pas admin-post) + caps forcées au clic
 */
if (!defined('LDGA_BOOTSTRAP')) {
  define('LDGA_BOOTSTRAP', 'v3.0');

  /* ---------- Rôle & capacités ---------- */
  add_action('init', function(){
    if (!get_role('lms_admin')) {
      add_role('lms_admin', 'Administrateur LMS', ['read'=>true]);
    }
    if ($r = get_role('lms_admin')) {
      foreach ([
        'read',
        // génériques WP (souvent requis par des plugins de restriction)
        'edit_posts','delete_posts','publish_posts','edit_others_posts','delete_others_posts',
        // caps LearnDash pour CPT "groups"
        'read_group','read_private_groups','edit_group','edit_groups','edit_others_groups','edit_published_groups',
        'publish_groups','delete_group','delete_groups','delete_private_groups','delete_published_groups','delete_others_groups',
        // au cas où un plugin vérifie “options”
        'manage_options'
      ] as $cap) { if (!$r->has_cap($cap)) $r->add_cap($cap); }
    }
  }, 1);

  /* ---------- Helpers ---------- */
  if (!function_exists('ldga_can_manage')) {
    function ldga_can_manage(){
      if (!is_user_logged_in()) return false;
      $u = wp_get_current_user();
      return (bool) array_intersect(['administrator','lms_admin'], (array)$u->roles);
    }
  }
  if (!function_exists('ldga_current_page_url')) {
    function ldga_current_page_url(){
      $base = get_permalink();
      if (!$base) $base = home_url(add_query_arg([], $_SERVER['REQUEST_URI']));
      $keep = ['ldga_s','ldga_paged','ldga_msg','ldga_err'];
      $qs = [];
      foreach($keep as $k) if(isset($_GET[$k]) && !is_array($_GET[$k])) $qs[$k] = sanitize_text_field($_GET[$k]);
      return add_query_arg($qs, $base);
    }
  }

  /* ---------- Shortcode UI ---------- */
  add_shortcode('ld_group_admin', function($atts){
    if (!ldga_can_manage()) return '<div style="color:#b91c1c">Accès restreint.</div>';

    $a = shortcode_atts(['per_page'=>20,'search'=>'yes'], $atts, 'ld_group_admin');
    $per_page    = max(5,(int)$a['per_page']);
    $show_search = (strtolower($a['search'])==='yes');

    $search = sanitize_text_field($_GET['ldga_s'] ?? '');
    $paged  = max(1,(int)($_GET['ldga_paged'] ?? 1));

    $q = new WP_Query([
      'post_type'      => 'groups',
      'post_status'    => ['publish'], // uniquement publiés
      'paged'          => $paged,
      'posts_per_page' => $per_page,
      's'              => $search,
      'orderby'        => 'title',
      'order'          => 'ASC',
      'no_found_rows'  => false,
    ]);

    $uid    = 'ldga_'.wp_generate_password(6,false,false);
    $nonce  = wp_create_nonce('ldga_delete_group_ajax');
    $ajax   = admin_url('admin-ajax.php');

    $notice = '';
    if (!empty($_GET['ldga_msg']) && $_GET['ldga_msg']==='deleted') {
      $notice .= '<div class="ldga-notice ok" style="margin:0 0 10px;padding:10px 12px;border:1px solid #c6f6d5;background:#ecfdf5;border-radius:8px;color:#065f46;">✅ Le groupe a été envoyé à la corbeille.</div>';
    }
    if (!empty($_GET['ldga_err'])) {
      $notice .= '<div class="ldga-notice err" style="margin:0 0 10px;padding:10px 12px;border:1px solid #fecaca;background:#fef2f2;border-radius:8px;color:#991b1b;">❌ Erreur : '.esc_html($_GET['ldga_err']).'</div>';
    }

    ob_start(); ?>
    <div id="<?php echo esc_attr($uid); ?>" class="ldga-wrap" style="max-width:1100px;margin:0 auto;font-size:13px;">
      <?php echo $notice; ?>

      <?php if ($show_search): ?>
      <form method="get" action="<?php echo esc_url(get_permalink()); ?>" style="margin:0 0 12px;display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
        <input type="text" name="ldga_s" value="<?php echo esc_attr($search); ?>" placeholder="Rechercher un groupe…">
        <input type="hidden" name="ldga_paged" value="1">
        <button type="submit" class="button">Rechercher</button>
      </form>
      <?php endif; ?>

      <div style="overflow:auto">
        <table class="wp-list-table widefat fixed striped" style="min-width:700px;">
          <thead>
            <tr>
              <th style="width:70px;">ID</th>
              <th>Titre</th>
              <th style="width:120px;">Membres</th>
              <th style="width:110px;">Statut</th>
              <th style="width:120px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!$q->have_posts()): ?>
              <tr><td colspan="5">Aucun groupe.</td></tr>
            <?php else:
              while ($q->have_posts()): $q->the_post();
                $gid = get_the_ID();
                $members = function_exists('learndash_get_groups_user_ids') ? count((array) learndash_get_groups_user_ids($gid)) : 0;
            ?>
            <tr data-row="<?php echo (int)$gid; ?>">
              <td><?php echo (int)$gid; ?></td>
              <td><?php echo esc_html(get_the_title($gid)); ?></td>
              <td><?php echo (int)$members; ?></td>
              <td>Publié</td>
              <td>
                <button type="button" class="button button-small ldga-delete" data-gid="<?php echo (int)$gid; ?>" data-name="<?php echo esc_attr(get_the_title($gid)); ?>" style="padding:2px 8px;border-radius:6px;">Supprimer</button>
              </td>
            </tr>
            <?php endwhile; wp_reset_postdata(); endif; ?>
          </tbody>
        </table>
      </div>

      <?php
      $max = (int)$q->max_num_pages;
      if ($max > 1): ?>
        <div style="margin:10px 0;display:flex;gap:.3rem;flex-wrap:wrap;">
          <?php for ($i=1; $i<=$max; $i++):
            $url = add_query_arg(['ldga_s'=>$search,'ldga_paged'=>$i], get_permalink()); ?>
            <a href="<?php echo esc_url($url); ?>" style="padding:.3rem .6rem;border:1px solid #ddd;border-radius:.4rem;<?php echo $i==$paged?'font-weight:700;background:#f6f6f6;':''; ?>"><?php echo $i; ?></a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

      <!-- Modal light -->
      <div class="ldga-modal" aria-hidden="true" style="position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.45);z-index:9999;">
        <div class="ldga-card" style="background:#fff;border-radius:12px;max-width:500px;width:92%;box-shadow:0 10px 30px rgba(0,0,0,.2);overflow:hidden;">
          <div style="padding:16px 18px;border-bottom:1px solid #eee;font-weight:700;">Supprimer le groupe</div>
          <div style="padding:16px 18px;">
            <p class="ldga-modal-text" style="margin:0 0 12px;">Confirmer la suppression ? Le groupe sera envoyé à la corbeille.</p>
            <div style="display:flex;gap:.6rem;justify-content:flex-end;margin-top:10px;">
              <button type="button" class="button ldga-cancel">Annuler</button>
              <button type="button" class="button button-primary ldga-confirm" style="background:#dbd0be;border-color:#dbd0be;">Supprimer</button>
            </div>
          </div>
        </div>
      </div>

      <script>
      (function(){
        const root  = document.getElementById('<?php echo esc_js($uid); ?>');
        const modal = root.querySelector('.ldga-modal');
        const cancel= root.querySelector('.ldga-cancel');
        const confirmBtn = root.querySelector('.ldga-confirm');
        let current = { id:null, name:'', rowEl:null };

        function openModal(id, name, rowEl){
          current = { id:id, name:name, rowEl:rowEl };
          root.querySelector('.ldga-modal-text').textContent =
            'Supprimer le groupe "'+name+'" (#'+id+') ? Il sera envoyé à la corbeille.';
          modal.style.display='flex'; modal.setAttribute('aria-hidden','false');
        }
        function closeModal(){
          modal.style.display='none'; modal.setAttribute('aria-hidden','true');
        }
        cancel.addEventListener('click', closeModal);
        modal.addEventListener('click', e=>{ if(e.target===modal) closeModal(); });

        root.querySelectorAll('.ldga-delete').forEach(btn=>{
          btn.addEventListener('click', e=>{
            e.preventDefault();
            const id   = btn.getAttribute('data-gid');
            const name = btn.getAttribute('data-name') || ('#'+id);
            const row  = btn.closest('tr');
            openModal(id, name, row);
          });
        });

        confirmBtn.addEventListener('click', function(){
          if(!current.id) return;
          const fd = new FormData();
          fd.append('action','ldga_delete_group_ajax');
          fd.append('nonce','<?php echo esc_js($nonce); ?>');
          fd.append('gid', current.id);

          fetch('<?php echo esc_url($ajax); ?>', {
            method:'POST', credentials:'same-origin', body: fd
          }).then(r=>r.json()).then(data=>{
            if(data && data.success){
              if(current.rowEl){ current.rowEl.parentNode.removeChild(current.rowEl); }
              closeModal();
              // petite alerte verte
              const n = document.createElement('div');
              n.textContent = '✅ Le groupe a été envoyé à la corbeille.';
              n.style.cssText='margin:12px 0;padding:10px 12px;border:1px solid #c6f6d5;background:#ecfdf5;border-radius:8px;color:#065f46;';
              root.insertBefore(n, root.firstChild);
            } else {
              closeModal();
              const msg = (data && data.data && data.data.message) ? data.data.message : 'Erreur inconnue';
              const n = document.createElement('div');
              n.textContent = '❌ '+msg;
              n.style.cssText='margin:12px 0;padding:10px 12px;border:1px solid #fecaca;background:#fef2f2;border-radius:8px;color:#991b1b;';
              root.insertBefore(n, root.firstChild);
            }
          }).catch(()=>{
            closeModal();
            const n = document.createElement('div');
            n.textContent = '❌ Erreur réseau';
            n.style.cssText='margin:12px 0;padding:10px 12px;border:1px solid #fecaca;background:#fef2f2;border-radius:8px;color:#991b1b;';
            root.insertBefore(n, root.firstChild);
          });
        });
      })();
      </script>
    </div>
    <?php
    return ob_get_clean();
  });

  /* ---------- AJAX: suppression groupe ---------- */
  add_action('wp_ajax_ldga_delete_group_ajax', function(){
    if (!ldga_can_manage()) wp_send_json_error(['message'=>'Accès restreint.'], 403);
    check_ajax_referer('ldga_delete_group_ajax','nonce');

    $gid = absint($_POST['gid'] ?? 0);
    if (!$gid) wp_send_json_error(['message'=>'ID manquant'], 400);

    $post = get_post($gid);
    if (!$post || $post->post_type!=='groups') wp_send_json_error(['message'=>'Groupe introuvable'], 404);

    // ✅ Force les caps de suppression pour administrator & lms_admin sur CE post
    $grant = function($allcaps, $caps, $args, $user) use ($gid) {
      $requested = $args[0] ?? '';
      $obj_id    = $args[2] ?? 0;
      if ($obj_id == $gid && !empty(array_intersect(['administrator','lms_admin'], (array)$user->roles))) {
        if (in_array($requested, ['delete_post','delete_group','delete_groups','delete_published_groups','delete_others_groups'], true)) {
          foreach ((array)$caps as $c) { $allcaps[$c] = true; }
          $allcaps['delete_post']   = true;
          $allcaps['delete_group']  = true;
          $allcaps['delete_groups'] = true;
        }
      }
      return $allcaps;
    };
    add_filter('user_has_cap', $grant, 20, 4);

    $ok = wp_trash_post($gid); // ou wp_delete_post($gid, true) pour supprimer définitivement
    remove_filter('user_has_cap', $grant, 20);

    if (!$ok) wp_send_json_error(['message'=>'Échec de la suppression'], 500);
    wp_send_json_success(['deleted'=>$gid]);
  });
}

// === Score card ===
add_shortcode('ld_score_card', function($atts){
  $a = shortcode_atts([
    'user_id'  => get_current_user_id(),
    'weights'  => '1,1,1',
    'cut1'     => '5',
    'cut2'     => '10',
    'max'      => '10',
    'decimals' => '1',
    'size'     => 'md'
  ], $atts, 'ld_score_card');

  $uid = (int)$a['user_id'];
  if (!$uid) return '';

  /* ---------- CSS une seule fois ---------- */
  static $printed = false;
  if (!$printed){
    $printed = true; ?>
    <style>
      :root{
        --sc-primary:#2E3143;
        --sc-accent:#dbd0be;
        --sc-info:#BCDFFE;
        --sc-surface:#ffffff;
        --sc-text:#2E3143;
        --sc-muted:#E9EFFB;
      }
      .ld-score{
        display:flex;flex-direction:column;gap:.65rem;
        background:var(--sc-surface);
        border:2px solid var(--sc-muted);
        border-radius:20px;padding:1.1rem 1.25rem;
        box-shadow:0 8px 18px rgba(18,28,45,.06);
        color:var(--sc-text);min-width:260px;max-width:420px
      }
      .ld-score--sm{padding:.8rem 1rem;border-radius:16px}
      .ld-score--lg{padding:1.2rem 1.4rem;border-radius:24px}

      .ld-chip{
        display:inline-flex;align-items:center;gap:.5rem;
        padding:.3rem .6rem;border-radius:999px;
        font-weight:800;font-size:.85rem
      }
      .ld-chip--explorateur{background:var(--sc-info);border:1px solid var(--sc-info);color:var(--sc-primary)}
      .ld-chip--actif{background:rgba(255,111,89,.12);border:1px solid var(--sc-accent);color:var(--sc-accent)}
      .ld-chip--expert{background:rgba(46,49,67,.12);border:1px solid var(--sc-primary);color:var(--sc-primary)}

      .ld-score__top{display:flex;align-items:center;justify-content:space-between}
      .ld-score__title{font-weight:900;letter-spacing:.2px;opacity:.85}
      .ld-score--sm .ld-score__title{font-size:.95rem}
      .ld-score--md .ld-score__title{font-size:1.05rem}
      .ld-score--lg .ld-score__title{font-size:1.15rem}

      .ld-score__value{font-weight:900;line-height:1}
      .ld-score--sm .ld-score__value{font-size:2rem}
      .ld-score--md .ld-score__value{font-size:2.4rem}
      .ld-score--lg .ld-score__value{font-size:2.8rem}
      .ld-score__value small{font-size:.55em;font-weight:800;opacity:.7;margin-left:.1em}

      .ld-score__bar{height:12px;border-radius:999px;background:var(--sc-muted);position:relative;overflow:visible}
      .ld-score__bar>span{position:absolute;inset:0 auto 0 0;width:0;border-radius:999px}
      .ld-score__bar--blue{color:var(--sc-info)}
      .ld-score__bar--blue>span{background:var(--sc-info)}
      .ld-score__bar--green{color:var(--sc-accent)}
      .ld-score__bar--green>span{background:var(--sc-accent)}
      .ld-score__bar--gold{color:var(--sc-primary)}
      .ld-score__bar--gold>span{background:var(--sc-primary)}

      .ld-score__marker{position:absolute;top:50%;transform:translate(-50%,-50%);color:currentColor}
      .ld-score__marker::before{
        content:"";display:block;width:10px;height:10px;border-radius:50%;
        background:#fff;border:2px solid currentColor;box-shadow:0 0 0 2px #fff
      }
      .ld-score__marker b{
        position:absolute;top:-170%;left:50%;transform:translateX(-50%);
        font-size:.72rem;font-weight:900;white-space:nowrap;background:#fff;
        border:1px solid var(--sc-muted);padding:.15rem .45rem;border-radius:8px;color:inherit
      }

      .ld-score__foot{display:flex;align-items:center;justify-content:space-between;font-weight:700;opacity:.9}
    </style>
  <?php }

  $seconds = (int) get_user_meta($uid, 'ld_total_seconds', true);
  $hours   = $seconds / 3600;
  $lessons = (int) do_shortcode('[ld_stat type="lessons_completed" user_id="'.$uid.'"]');
  $inter   = (int) do_shortcode('[ld_stat type="interactions" user_id="'.$uid.'"]');

  $w   = array_map('floatval', explode(',', $a['weights']));
  $w   = array_pad($w, 3, 1);
  $sum = max(array_sum($w), 1);

  $score = (($hours*$w[0]) + ($lessons*$w[1]) + ($inter*$w[2])) / $sum;
  $score = round($score, (int)$a['decimals']);

  $cut1 = (float)$a['cut1']; // Acteur (ancien Actif)
  $cut2 = (float)$a['cut2']; // Expert
  $max  = max(0.0001, (float)$a['max']);

  $level    = 'Explorateur';
  $chip     = 'explorateur';
  $barCls   = 'ld-score__bar--blue';
  $nextLabel= 'Acteur';
  $next     = $cut1;

  if ($score >= $cut2){
    $level='Expert';
    $chip='expert';
    $barCls='ld-score__bar--gold';
    $nextLabel='Max';
  } elseif ($score >= $cut1){
    $level='Acteur';
    $chip='actif';
    $barCls='ld-score__bar--green';
    $nextLabel='Expert';
    $next=$cut2;
  }

  $barPct   = max(0, min(100, 100 * $score / $max));
  $markerPct= ($nextLabel==='Max') ? null : max(0, min(100, 100 * $next / $max));
  $size     = in_array($a['size'], ['sm','md','lg'], true) ? $a['size'] : 'md';

  ob_start(); ?>
  <div class="ld-score ld-score--<?php echo esc_attr($size); ?>">
    <div class="ld-score__top">
      <div class="ld-score__title">Score d’implication</div>
      <div class="ld-chip ld-chip--<?php echo esc_attr($chip); ?>"><?php echo esc_html($level); ?></div>
    </div>

    <div class="ld-score__value">
      <?php echo number_format($score, (int)$a['decimals'], ',', ' '); ?><small>pts</small>
    </div>

    <div class="ld-score__bar <?php echo esc_attr($barCls); ?>">
      <span style="width:<?php echo esc_attr($barPct); ?>%"></span>
      <?php if (!is_null($markerPct)): ?>
        <i class="ld-score__marker" style="left:<?php echo esc_attr($markerPct); ?>%"><b><?php echo esc_html($nextLabel); ?></b></i>
      <?php endif; ?>
    </div>

    <div class="ld-score__foot">
      <?php if ($nextLabel==='Max'): ?>
        <small>Niveau max atteint</small><small>&nbsp;</small>
      <?php else: ?>
        <small>Prochain niveau : <?php echo esc_html($nextLabel); ?></small><small>&nbsp;</small>
      <?php endif; ?>
    </div>
  </div>
  <?php
  return ob_get_clean();
});


// === Badge ===
add_shortcode('ld_levels', function($atts){
  $a = shortcode_atts([
    'user_id'  => get_current_user_id(),
    'weights'  => '1,1,1',
    'cut1'     => '5',
    'cut2'     => '10',
    'decimals' => '1',
    'size'     => 'md',
    'layout'   => 'vertical'
  ], $atts, 'ld_levels');

  $uid = (int)$a['user_id'];
  if (!$uid) return '';

  static $printed = false;
  if (!$printed){
    $printed = true; ?>
    <style>
      :root{
        --lv-primary:#2E3143;
        --lv-accent:#dbd0be;
        --lv-info:#BCDFFE;
        --lv-bg:#ffffff;
        --lv-muted:#E9EFFB;
        --lv-text:#2E3143;
      }

      .ld-levels{display:flex;gap:12px}
      .ld-levels--vertical{flex-direction:column}
      .ld-levels--horizontal{flex-direction:row;flex-wrap:wrap}

      .ld-level{
        display:flex;align-items:center;justify-content:space-between;
        gap:12px;border-radius:14px;padding:.75rem 1rem;border:2px solid transparent;
        background:var(--lv-bg);position:relative;min-width:260px;
        color:var(--lv-text);
        box-shadow:0 4px 8px rgba(0,0,0,.04);
        transition:all .25s ease;
      }
      .ld-level__left{display:flex;align-items:center;gap:.6rem}
      .ld-level__badge{
        display:inline-flex;align-items:center;justify-content:center;
        width:2rem;height:2rem;border-radius:50%;
        background:var(--lv-muted);color:var(--lv-text);font-weight:700
      }
      .ld-level__title{font-weight:800;letter-spacing:.2px}
      .ld-level__right{font-weight:700;white-space:nowrap}
      .ld-level__right small{font-weight:600;opacity:.9}

      .ld-levels--sm .ld-level{padding:.55rem .8rem}
      .ld-levels--sm .ld-level__badge{width:1.6rem;height:1.6rem}
      .ld-levels--sm .ld-level__title{font-size:.95rem}
      .ld-levels--md .ld-level__title{font-size:1rem}
      .ld-levels--lg .ld-level{padding:1rem 1.25rem}
      .ld-levels--lg .ld-level__badge{width:2.2rem;height:2.2rem}
      .ld-levels--lg .ld-level__title{font-size:1.1rem}

      .ld-level--explorateur.ld-level--ok{
        background:var(--lv-info);
        border-color:var(--lv-info);
        color:var(--lv-primary);
      }
      .ld-level--actif.ld-level--ok{
        background:rgba(255,111,89,.12);
        border-color:var(--lv-accent);
        color:var(--lv-accent);
      }
      .ld-level--expert.ld-level--ok{
        background:rgba(46,49,67,.12);
        border-color:var(--lv-primary);
        color:var(--lv-primary);
      }

      .ld-level--lock{filter:grayscale(1);opacity:.7}

      .ld-ico{width:1.1rem;height:1.1rem;display:inline-block;vertical-align:-2px}
      .ld-ico--check{color:currentColor}
      .ld-ico--lock{color:#999}
    </style>
  <?php }

  $seconds = (int) get_user_meta($uid, 'ld_total_seconds', true);
  $hours   = $seconds / 3600;
  $lessons = (int) do_shortcode('[ld_stat type="lessons_completed" user_id="'.$uid.'"]');
  $inter   = (int) do_shortcode('[ld_stat type="interactions" user_id="'.$uid.'"]');

  $w   = array_map('floatval', explode(',', $a['weights']));
  $w   = array_pad($w, 3, 1);
  $sum = max(array_sum($w), 1);

  $score = (($hours*$w[0]) + ($lessons*$w[1]) + ($inter*$w[2])) / $sum;
  $score = round($score, (int)$a['decimals']);

  $cut1 = (float)$a['cut1'];
  $cut2 = (float)$a['cut2'];

  $levels = [
    ['slug'=>'explorateur','label'=>'Explorateur','t'=>0],
    ['slug'=>'actif','label'=>'Acteur','t'=>$cut1],
    ['slug'=>'expert','label'=>'Expert','t'=>$cut2],
  ];

  $ico_check = '<svg class="ld-ico ld-ico--check" viewBox="0 0 24 24"><path fill="currentColor" d="M20.3 5.7 9 17l-5.3-5.3 1.4-1.4L9 14.2l9.9-9.9 1.4 1.4Z"/></svg>';
  $ico_lock  = '<svg class="ld-ico ld-ico--lock" viewBox="0 0 24 24"><path fill="currentColor" d="M17 9V7a5 5 0 0 0-10 0v2H5v12h14V9h-2ZM9 7a3 3 0 1 1 6 0v2H9V7Zm3 5a2 2 0 0 1 1 3.732V18h-2v-2.268A2 2 0 0 1 12 12Z"/></svg>';

  $size   = in_array($a['size'], ['sm','md','lg'], true) ? $a['size'] : 'md';
  $layout = (strtolower($a['layout']) === 'horizontal') ? 'horizontal' : 'vertical';

  ob_start(); ?>
  <div class="ld-levels ld-levels--<?php echo esc_attr($layout) ?> ld-levels--<?php echo esc_attr($size) ?>">
    <?php foreach ($levels as $L):
      $ok   = ($score >= $L['t']);
      $rest = max(0, round($L['t'] - $score, (int)$a['decimals']));
      $state_cls = $ok ? 'ld-level--ok' : 'ld-level--lock'; ?>
      <div class="ld-level ld-level--<?php echo esc_attr($L['slug'].' '.$state_cls); ?>">
        <div class="ld-level__left">
          <div class="ld-level__badge"><?php echo strtoupper(substr($L['label'],0,1)); ?></div>
          <div class="ld-level__title"><?php echo esc_html($L['label']); ?></div>
        </div>
        <div class="ld-level__right">
          <?php if ($ok): ?>
            <?php echo $ico_check; ?> <small>Atteint</small>
          <?php else: ?>
            <?php echo $ico_lock; ?> <small>Reste <?php echo number_format($rest, (int)$a['decimals'], ',', ' '); ?> pts</small>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php return ob_get_clean();
});


// === Gencertificat ===
/**
 * LearnDash — Durée & Objectifs par parcours + shortcodes
 * Coller dans functions.php (enfant) ou faire un petit mu-plugin.
 */
if (!defined('ABSPATH')) exit;

/** ---------- Metabox : champs sur les cours ---------- */
add_action('add_meta_boxes', function () {
  add_meta_box(
    'ay_ld_parcours_infos',
    'Infos du parcours (Durée & Objectifs)',
    function ($post) {
      wp_nonce_field('ay_ld_parcours_infos', 'ay_ld_parcours_infos_nonce');
      $duree = get_post_meta($post->ID, 'ld_parcours_duree', true);
      $objectifs = get_post_meta($post->ID, 'ld_parcours_objectifs', true);
      ?>
      <style>
        .ayld-field{margin:12px 0;}
        .ayld-field label{font-weight:600;display:block;margin-bottom:6px}
        .ayld-field input[type="text"]{width:100%}
        .ayld-field textarea{width:100%;min-height:140px}
        .ayld-help{color:#666;font-size:12px;margin-top:4px}
      </style>

      <div class="ayld-field">
        <label for="ld_parcours_duree">Durée du parcours</label>
        <input type="text" id="ld_parcours_duree" name="ld_parcours_duree"
               value="<?php echo esc_attr($duree); ?>"
               placeholder="Ex. 6 h 30 min, ou 4 semaines">
        <div class="ayld-help">Texte libre (affiché tel quel).</div>
      </div>

      <div class="ayld-field">
        <label for="ld_parcours_objectifs">Objectifs pédagogiques</label>
        <textarea id="ld_parcours_objectifs" name="ld_parcours_objectifs"
                  placeholder="1 objectif par ligne"><?php echo esc_textarea($objectifs); ?></textarea>
        <div class="ayld-help">Saisis un objectif par ligne. Le shortcode peut les afficher en liste &lt;ul&gt;.</div>
      </div>
      <?php
    },
    'sfwd-courses',
    'normal',
    'default'
  );
});

add_action('save_post_sfwd-courses', function ($post_id) {
  if (!isset($_POST['ay_ld_parcours_infos_nonce']) || !wp_verify_nonce($_POST['ay_ld_parcours_infos_nonce'], 'ay_ld_parcours_infos')) return;
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
  if (!current_user_can('edit_post', $post_id)) return;

  // Durée = simple texte
  if (isset($_POST['ld_parcours_duree'])) {
    $val = sanitize_text_field(wp_unslash($_POST['ld_parcours_duree']));
    update_post_meta($post_id, 'ld_parcours_duree', $val);
  }

  // Objectifs = conserver retours ligne / un minimum de HTML s'il y en a
  if (isset($_POST['ld_parcours_objectifs'])) {
    $raw = wp_unslash($_POST['ld_parcours_objectifs']);
    // Normaliser les retours à \n
    $raw = preg_replace("/\r\n|\r/", "\n", $raw);
    // Autoriser un HTML simple
    $val = wp_kses_post($raw);
    update_post_meta($post_id, 'ld_parcours_objectifs', $val);
  }
}, 10, 1);

/** ---------- Helper : détecter le course_id ---------- */
function ay_ld_detect_course_id($given_attr = '') {
  // Autoriser un shortcode dans l'attribut (ex: [courseinfo ...])
  $given_attr = do_shortcode((string)$given_attr);
  if (is_numeric($given_attr) && (int)$given_attr > 0) {
    return (int)$given_attr;
  }

  // Si on est sur la page d’un cours
  if (is_singular('sfwd-courses')) {
    return get_the_ID();
  }

  // Contexte certificat : LearnDash passe souvent ?course_id= ou ?course=
  foreach (['course_id','course','cid'] as $k) {
    if (isset($_GET[$k]) && absint($_GET[$k])) {
      return absint($_GET[$k]);
    }
  }

  return 0; // non trouvé
}

/** ---------- Shortcode : Durée ---------- *
 * [ld_course_duration course_id=""] 
 * Options : label, tag, class, before, after, empty
 */
add_shortcode('ld_course_duration', function ($atts) {
  $a = shortcode_atts([
    'course_id' => '',
    'label'     => '',
    'tag'       => 'span',
    'class'     => '',
    'before'    => '',
    'after'     => '',
    'empty'     => ''
  ], $atts, 'ld_course_duration');

  $cid = ay_ld_detect_course_id($a['course_id']);
  $val = $cid ? get_post_meta($cid, 'ld_parcours_duree', true) : '';

  if ($val === '') $val = $a['empty'];
  if ($val === '') return '';

  $label = $a['label'] !== '' ? '<strong>'.esc_html($a['label']).'</strong> ' : '';
  $tag   = preg_replace('/[^a-z0-9:_-]/i', '', $a['tag']); if (!$tag) $tag = 'span';
  $cls   = $a['class'] ? ' class="'.esc_attr($a['class']).'"' : '';
  $html  = esc_html($val);

  return $a['before']."<{$tag}{$cls}>{$label}{$html}</{$tag}>".$a['after'];
});

/** ---------- Shortcode : Objectifs ---------- *
 * [ld_course_objectives course_id=""]
 * Options : label, tag, class, format=ul|br|raw, empty
 *  - ul (defaut) : 1 ligne = 1 <li>
 *  - br : conserve le texte en insérant <br>
 *  - raw : affiche le HTML autorisé tel quel
 */
add_shortcode('ld_course_objectives', function ($atts) {
  $a = shortcode_atts([
    'course_id' => '',
    'label'     => '',
    'tag'       => 'div',
    'class'     => '',
    'format'    => 'ul',
    'empty'     => ''
  ], $atts, 'ld_course_objectives');

  $cid = ay_ld_detect_course_id($a['course_id']);
  $val = $cid ? get_post_meta($cid, 'ld_parcours_objectifs', true) : '';

  if ($val === '') $val = $a['empty'];
  if ($val === '') return '';

  $content = '';
  $format  = strtolower($a['format']);

  if ($format === 'ul') {
    $lines = preg_split('/\n+/', $val);
    $lis = '';
    foreach ($lines as $line) {
      $line = trim(wp_strip_all_tags($line));
      if ($line !== '') $lis .= '<li>'.esc_html($line).'</li>';
    }
    if ($lis === '') return '';
    $content = "<ul>{$lis}</ul>";
  } elseif ($format === 'br') {
    $content = nl2br(esc_html($val));
  } else { // raw
    $content = wp_kses_post($val);
  }

  $label = $a['label'] !== '' ? '<strong>'.esc_html($a['label']).'</strong> ' : '';
  $tag   = preg_replace('/[^a-z0-9:_-]/i', '', $a['tag']); if (!$tag) $tag = 'div';
  $cls   = $a['class'] ? ' class="'.esc_attr($a['class']).'"' : '';

  return "<{$tag}{$cls}>{$label}{$content}</{$tag}>";
});
// === Objectifs — version INLINE (une seule ligne, sans label) ===
if (!defined('ABSPATH')) exit;

// Helper (au cas où il n'existe pas déjà)
if (!function_exists('ay_ld_detect_course_id')) {
  function ay_ld_detect_course_id($given_attr=''){
    $given_attr = do_shortcode((string)$given_attr);
    if (is_numeric($given_attr) && (int)$given_attr > 0) return (int)$given_attr;
    if (is_singular('sfwd-courses')) return get_the_ID();
    foreach (['course_id','course','cid'] as $k) if (isset($_GET[$k]) && absint($_GET[$k])) return absint($_GET[$k]);
    return 0;
  }
}

add_shortcode('ld_course_objectives_inline', function($atts){
  $a = shortcode_atts([
    'course_id' => '',
    'class'     => '',
    // optionnel: 'nowrap' => 'no'  // passe 'yes' si tu veux empêcher tout retour à la ligne
  ], $atts, 'ld_course_objectives_inline');

  $cid = ay_ld_detect_course_id($a['course_id']);
  $val = $cid ? get_post_meta($cid, 'ld_parcours_objectifs', true) : '';
  if ($val === '') return '';

  // 1 ligne propre : on enlève le HTML, on compacte les espaces / retours
  $text = wp_strip_all_tags($val);
  $text = preg_replace('/\s+/u', ' ', $text);
  $text = trim($text);

  $cls  = $a['class'] ? ' class="'.esc_attr($a['class']).'"' : '';
  return '<span'.$cls.'>'.$text.'</span>';
});

// === cacher espace ===


// === DATALAB V2.2 ===
/**
 * Plugin Name: LD Topic Report (Par module)
 * Description: Filtre Parcours → Module (topic) et affiche les stats : Inscrits, Pas commencé, En cours, Terminé, % complétion, Dernière activité.
 * Version:     1.1.0
 */

if (!defined('ABSPATH')) exit;

/* ========== Helpers ========== */
function ldtr_i($v){ return intval($v ?? 0); }
function ldtr_e($s){ return esc_html($s); }
function ldtr_is_valid_dt($dt){
  if (!$dt) return false;
  if (is_numeric($dt)) return (intval($dt) > 0);
  $dt = trim((string)$dt);
  if ($dt === '0000-00-00 00:00:00') return false;
  return (bool) strtotime($dt.' UTC');
}
function ldtr_fmt_dt($dt){
  if (!ldtr_is_valid_dt($dt)) return '—';
  $ts = is_numeric($dt) ? intval($dt) : strtotime($dt.' UTC');
  return wp_date('Y-m-d H:i', $ts);
}

/**
 * Inscrits au parcours = union de toutes les sources (LD + Groupes LD + Uncanny Groups + héritage).
 * $exclude_roles_csv ex: "administrator,editor"
 */
function ldtr_get_enrolled_user_ids($course_id, $exclude_roles_csv=''){
  global $wpdb;
  $course_id = (int)$course_id;
  $ids = [];

  // 1) API LearnDash (direct + via groupes + LMS shops) — source principale
  if (function_exists('learndash_get_users_for_course')) {
    $users = learndash_get_users_for_course($course_id, true, ['number' => -1]); // bypass cache
    foreach ((array)$users as $u){
      $ids[] = (int)(is_object($u) && isset($u->ID) ? $u->ID : (int)$u);
    }
  }

  // 2) usermeta posé par LD/UG (très fiable)
  $meta_key = 'course_' . $course_id . '_access_from';
  $ids = array_merge($ids, array_map('intval', (array)$wpdb->get_col(
    $wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key=%s", $meta_key)
  )));

  // 3) CSV d’accès direct historique
  $csv = get_post_meta($course_id, 'course_access_list', true);
  if (!empty($csv)) {
    $ids = array_merge($ids, array_map('intval',
      array_filter(array_map('trim', explode(',', (string)$csv)))
    ));
  }

  // 4) Groupes LearnDash natifs qui ont ce cours
  $group_ids = array_map('intval', (array)$wpdb->get_col($wpdb->prepare(
    "SELECT post_id
       FROM $wpdb->postmeta
      WHERE meta_key='learndash_group_enrolled_courses'
        AND (meta_value=%s OR FIND_IN_SET(%d, meta_value))",
    (string)$course_id, (int)$course_id
  )));
  if ($group_ids){
    $tbl = $wpdb->prefix.'learndash_user_groups';
    $has_tbl = $wpdb->get_var( $wpdb->prepare("SHOW TABLES LIKE %s", $tbl) );
    if ($has_tbl === $tbl){
      $in = implode(',', array_map('intval', $group_ids));
      $ids = array_merge($ids, array_map('intval',
        (array)$wpdb->get_col("SELECT DISTINCT user_id FROM $tbl WHERE group_id IN ($in)")
      ));
    }
  }

  // 5) Uncanny Groups (si présence des tables)
  $ulgm_uc = $wpdb->prefix.'ulgm_user_courses';
  if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $ulgm_uc)) === $ulgm_uc){
    $ids = array_merge($ids, array_map('intval', (array)$wpdb->get_col(
      $wpdb->prepare("SELECT DISTINCT user_id FROM $ulgm_uc WHERE course_id=%d", (int)$course_id)
    )));
  }
  $ulgm_gu = $wpdb->prefix.'ulgm_group_users';
  $ulgm_gc = $wpdb->prefix.'ulgm_group_courses';
  if (
    $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $ulgm_gu)) === $ulgm_gu &&
    $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $ulgm_gc)) === $ulgm_gc
  ){
    $ids = array_merge($ids, array_map('intval', (array)$wpdb->get_col(
      $wpdb->prepare(
        "SELECT DISTINCT gu.user_id
           FROM $ulgm_gu gu
           JOIN $ulgm_gc gc ON gc.group_id = gu.group_id
          WHERE gc.course_id = %d",
        (int)$course_id
      )
    )));
  }

  // Déduplication
  $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

  // Exclusion de rôles si demandé
  $exclude_roles = array_filter(array_map('trim', explode(',', (string)$exclude_roles_csv)));
  if ($ids && $exclude_roles){
    $users = get_users(['include'=>$ids, 'fields'=>['ID','roles']]);
    $keep = [];
    foreach ($users as $u){
      if (!array_intersect((array)$u->roles, $exclude_roles)){
        $keep[] = (int)$u->ID;
      }
    }
    $ids = $keep;
  }

  return $ids;
}

/** Tous les topics (modules) d’un parcours */
function ldtr_get_topics_for_course($course_id){
  $topics = [];
  if (function_exists('learndash_get_lesson_list') && function_exists('learndash_get_topic_list')){
    $lessons = learndash_get_lesson_list((int)$course_id);
    foreach ((array)$lessons as $l){
      $ts = learndash_get_topic_list((int)$l->ID, (int)$course_id);
      foreach ((array)$ts as $t) $topics[] = (int)$t->ID;
    }
  }
  return $topics;
}

/** Stats pour 1 topic (module) parmi les inscrits du parcours */
function ldtr_compute_topic_stats($course_id, $topic_id, $exclude_roles_csv=''){
  $course_id = (int)$course_id;
  $topic_id  = (int)$topic_id;

  $enrolled_ids = ldtr_get_enrolled_user_ids($course_id, $exclude_roles_csv);
  $total = count($enrolled_ids);
  if ($total === 0){
    return ['inscrits'=>0,'pas'=>0,'encours'=>0,'termine'=>0,'percent'=>0,'last'=>'—'];
  }

  global $wpdb;
  $ua = $wpdb->prefix.'learndash_user_activity';
  $in = implode(',', array_map('intval', $enrolled_ids));

  // Agrégat par user sur ce topic : status = MAX(status), last = MAX(updated)
  $rows = $wpdb->get_results($wpdb->prepare(
    "SELECT user_id,
            MAX(activity_status)   AS status,
            MAX(activity_updated)  AS updated
       FROM $ua
      WHERE course_id=%d AND post_id=%d AND activity_type='topic'
        AND user_id IN ($in)
   GROUP BY user_id",
    $course_id, $topic_id
  ));

  $opened = 0; $termine = 0; $last = null;
  foreach ((array)$rows as $r){
    $opened++;
    if (intval($r->status) === 1) $termine++;
    if (ldtr_is_valid_dt($r->updated)){
      $ts = is_numeric($r->updated) ? intval($r->updated) : strtotime($r->updated.' UTC');
      if (!$last || $ts > $last) $last = $ts;
    }
  }

  $encours = max(0, $opened - $termine);
  $pas     = max(0, $total - $opened);
  $percent = ($total>0) ? round($termine*100/$total) : 0;

  return [
    'inscrits'=>$total,
    'pas'=>$pas,
    'encours'=>$encours,
    'termine'=>$termine,
    'percent'=>$percent,
    'last'=> ($last ? wp_date('Y-m-d H:i', $last) : '—')
  ];
}

/* ========== AJAX endpoints ========== */

/** Liste les modules (topics) d’un parcours */
add_action('wp_ajax_ldtr_get_topics', function(){
  if (!is_user_logged_in()) wp_send_json_error('forbidden', 403);
  check_ajax_referer('ldtr_nonce', 'nonce');

  $course_id = ldtr_i($_POST['course_id'] ?? 0);
  $items = [];
  if ($course_id){
    foreach (ldtr_get_topics_for_course($course_id) as $tid){
      $items[] = ['id'=>$tid, 'title'=> get_the_title($tid)];
    }
  }
  wp_send_json_success(['items'=>$items]);
});

/** Renvoie les stats d’un module */
add_action('wp_ajax_ldtr_topic_stats', function(){
  if (!is_user_logged_in()) wp_send_json_error('forbidden', 403);
  check_ajax_referer('ldtr_nonce', 'nonce');

  $course_id = ldtr_i($_POST['course_id'] ?? 0);
  $topic_id  = ldtr_i($_POST['topic_id']  ?? 0);
  $exclude   = sanitize_text_field($_POST['exclude_roles'] ?? '');

  if (!$course_id || !$topic_id) wp_send_json_error('missing params', 400);

  $stats = ldtr_compute_topic_stats($course_id, $topic_id, $exclude);
  wp_send_json_success(['stats'=>$stats]);
});

/* ========== Shortcode & assets ========== */
add_action('wp_enqueue_scripts', function(){
  wp_register_script('ldtr-js', false, [], null, true);
});

/** Shortcode : [ld_topic_report exclude_roles="administrator,editor"] */
add_shortcode('ld_topic_report', function($atts){
  if (!is_user_logged_in()) return '<em>Connecte-toi pour voir ce rapport.</em>';

  $a = shortcode_atts([
    'exclude_roles' => '', // ex: "administrator,editor"
  ], $atts, 'ld_topic_report');

  // Liste Parcours
  $courses = get_posts([
    'post_type'   => 'sfwd-courses',
    'post_status' => 'publish',
    'numberposts' => -1,
    'orderby'     => 'title',
    'order'       => 'ASC',
    'fields'      => 'ids',
  ]);

  ob_start(); ?>
  <div class="ldtr" data-exclude="<?php echo esc_attr($a['exclude_roles']); ?>">
    <form class="ldtr-f" onsubmit="return false;" style="display:flex;flex-wrap:wrap;gap:12px;align-items:end;margin:10px 0">
      <label>Parcours<br>
        <select class="ldtr-course">
          <option value="">— Choisir un parcours —</option>
          <?php foreach ($courses as $cid): ?>
            <option value="<?php echo esc_attr($cid); ?>"><?php echo esc_html(get_the_title($cid)); ?></option>
          <?php endforeach; ?>
        </select>
      </label>

      <label>Module<br>
        <select class="ldtr-topic" disabled>
          <option value="">— Choisir —</option>
        </select>
      </label>

      <span class="ldtr-status" style="min-width:160px;color:#666"></span>
    </form>

    <div class="ldtr-results"></div>
  </div>
  <?php
  $html = ob_get_clean();

  // Inline JS (AJAX)
  wp_enqueue_script('ldtr-js');
  $ajaxurl = admin_url('admin-ajax.php');
  $nonce   = wp_create_nonce('ldtr_nonce');

  $inline = <<<JS
  (function(){
    function q(s,c){return (c||document).querySelector(s)}
    function qa(s,c){return (c||document).querySelectorAll(s)}
    qa('.ldtr').forEach(function(root){
      var exclude = root.getAttribute('data-exclude')||'';
      var selCourse = q('.ldtr-course',root);
      var selTopic  = q('.ldtr-topic',root);
      var status    = q('.ldtr-status',root);
      var results   = q('.ldtr-results',root);

      function setStatus(m){ status.textContent=m||''; }
      function clear(){ results.innerHTML=''; }

      selCourse.addEventListener('change', function(){
        clear();
        var cid = selCourse.value;
        selTopic.innerHTML = '<option value="">— Choisir —</option>';
        selTopic.disabled = true;
        if(!cid){ setStatus(''); return; }
        setStatus('Chargement des modules…');

        var fd = new FormData();
        fd.append('action','ldtr_get_topics');
        fd.append('nonce','{$nonce}');
        fd.append('course_id', cid);

        fetch('{$ajaxurl}', {method:'POST', body:fd, credentials:'same-origin', cache:'no-store'})
          .then(r=>r.json())
          .then(function(j){
            setStatus('');
            if(!(j && j.success)) return;
            var items = j.data.items || [];
            if(!items.length){
              selTopic.innerHTML = '<option value="">(Aucun module)</option>';
              selTopic.disabled = true;
              return;
            }
            var opts=['<option value="">— Choisir —</option>'];
            items.forEach(function(it){ opts.push('<option value="'+String(it.id)+'">'+String(it.title||'')+'</option>'); });
            selTopic.innerHTML = opts.join('');
            selTopic.disabled = false;
            if(items.length===1){ selTopic.value=String(items[0].id); selTopic.dispatchEvent(new Event('change')); }
          })
          .catch(function(){ setStatus(''); });
      });

      selTopic.addEventListener('change', function(){
        clear();
        var cid = selCourse.value, tid = selTopic.value;
        if(!cid || !tid) return;
        setStatus('Calcul des statistiques…');

        var fd = new FormData();
        fd.append('action','ldtr_topic_stats');
        fd.append('nonce','{$nonce}');
        fd.append('course_id', cid);
        fd.append('topic_id',  tid);
        fd.append('exclude_roles', exclude);

        fetch('{$ajaxurl}', {method:'POST', body:fd, credentials:'same-origin', cache:'no-store'})
          .then(r=>r.json())
          .then(function(j){
            setStatus('');
            if(!(j && j.success)){ results.innerHTML='<div style="color:#a00">Erreur.</div>'; return; }
            var s = j.data.stats || {};
            var h = '';
            h += '<div style="overflow:auto"><table class="widefat striped"><thead><tr>';
            ['Parcours','Module','Inscrits','Pas commencé','En cours','Terminé','% complétion','Dernière activité'].forEach(function(th){
              h += '<th>'+th+'</th>';
            });
            h += '</tr></thead><tbody><tr>';
            h += '<td>' + (selCourse.options[selCourse.selectedIndex]?.text||'') + '</td>';
            h += '<td>' + (selTopic.options[selTopic.selectedIndex]?.text||'') + '</td>';
            h += '<td>' + (s.inscrits||0) + '</td>';
            h += '<td>' + (s.pas||0) + '</td>';
            h += '<td>' + (s.encours||0) + '</td>';
            h += '<td>' + (s.termine||0) + '</td>';
            h += '<td>' + ((s.inscrits>0)? (s.percent+'%') : '—') + '</td>';
            h += '<td>' + (s.last||'—') + '</td>';
            h += '</tr></tbody></table></div>';
            results.innerHTML = h;
          })
          .catch(function(){ setStatus(''); });
      });
    });
  })();
  JS;
  wp_add_inline_script('ldtr-js', $inline);

  return $html;
});

