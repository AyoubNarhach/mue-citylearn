<?php
namespace uncanny_learndash_reporting;

/**
 *
 */
class Translations {
	/**
	 * @return array
	 */
	public static function get_js_localized_strings() {

		// listed as they appear in file order under \plugins\tin-canny-learndash-reporting\src\assets\admin\js\scripts\components\*.js
		return array(
			// charts.js
			'In Progress'                                 => 'En cours',
			'Completed'                                   => 'Terminé',
			'Not Started'                                 => 'Non commencé',
			'No Data'                                     => 'Aucune donnée',
			// config.js
			'Are you sure you want to permanently delete all Tin Can data? This cannot be undone.' => 'Êtes-vous sûr de vouloir supprimer définitivement toutes les données Tin Can ? Cette action est irréversible.',
			'Are you sure you want to permanently delete all Bookmark data? This cannot be undone.' => 'Êtes-vous sûr de vouloir supprimer définitivement tous les marque-pages ? Cette action est irréversible.',
			// data-object.js
			'%'                                           => '%',
			// query-string.js
			'Please select your criteria to filter the Tin Can data.' => 'Veuillez sélectionner vos critères pour filtrer les données Tin Can.',
			'Please select your criteria to filter the xAPI Quiz data.' => 'Veuillez sélectionner vos critères pour filtrer les données xAPI Quiz.',
			// tables.js
			'Enrolled'                                    => 'Inscrit',
			'Avg Time to Complete'                        => 'Temps moyen de complétion',
			'Avg Time Spent'                              => 'Temps moyen passé',
			'% Complete'                                  => '% Complété',
			'Certificate Link'                            => 'Lien certificat',
			'Users Enrolled'                              => 'Apprenants inscrits',
			'Average Time to Complete'                    => 'Temps moyen pour compléter',
			'Average '                                    => 'Moyenne ',
			'Display Name'                                => 'Nom',
			'First Name'                                  => 'Prénom',
			'Last Name'                                   => 'Nom de famille',
			'Username'                                    => 'Identifiant',
			'Email Address'                               => 'Adresse e-mail',
			'Course Enrolled'                             => 'Formation inscrite',
			/* translators: %s is the "Quiz" label */
			'Quiz Average'                                => sprintf( '%s Moyenne', \LearnDash_Custom_Label::get_label( 'quiz' ) ),
			'Completion Date'                             => 'Date de complétion',
			'Time to Complete'                            => 'Temps pour compléter',
			'Time Spent'                                  => 'Temps passé',
			'Status'                                      => 'Statut',
			/* translators: %s is the "Lesson" label */
			'Associated Lesson'                           => sprintf( '%s associé(e)', \LearnDash_Custom_Label::get_label( 'lesson' ) ),
			'Name'                                        => 'Nom',
			'Date Completed'                              => 'Date de complétion',
			'Assignment Name'                             => 'Nom du devoir',
			'Approval'                                    => 'Approbation',
			'Submitted On'                                => 'Soumis le',
			'Module'                                      => 'Module',
			'Target'                                      => 'Cible',
			'Action'                                      => 'Action',
			'Result'                                      => 'Résultat',
			'Date'                                        => 'Date',
			'There are no activities to report.'          => 'Aucune activité à rapporter.',
			'CSV Export'                                  => 'Export CSV',
			'Excel Export'                                => 'Export Excel',
			/* translators: %s is the course label */
			'missingTitleCourseId'                        => 'ID formation : ',
			'Not Complete'                                => 'Non terminé',
			'Not Approved'                                => 'Non approuvé',
			'Approved'                                    => 'Approuvé',
			'Page'                                        => 'Page',
			'View'                                        => 'Voir',
			// tabs.js
			' Summary'                                    => ' Résumé',
			'< Users Overview'                            => '< Vue d\'ensemble des utilisateurs',
			'< User Overview'                             => '< Vue d\'ensemble de l\'utilisateur',
			'< Users '                                    => '< ',
			' Overview'                                   => ' Vue d\'ensemble',
			'Showing _START_ to _END_ of _TOTAL_ entries' => 'Affichage de _START_ à _END_ sur _TOTAL_ entrées',
			'Showing 0 to 0 of 0 entries'                 => 'Affichage de 0 à 0 sur 0 entrée',
			'(filtered from _MAX_ total entries)'         => '(filtré sur _MAX_ entrées au total)',
			'Show _MENU_ entries'                         => 'Afficher _MENU_ entrées',
			'Loading...'                                  => 'Chargement...',
			'Processing...'                               => 'Traitement...',
			'Search:'                                     => 'Rechercher :',
			'No matching records found'                   => 'Aucun résultat trouvé',
			'First'                                       => 'Premier',
			'Last'                                        => 'Dernier',
			'Next'                                        => 'Suivant',
			'Previous'                                    => 'Précédent',
			': activate to sort column ascending'         => ': activer pour trier par ordre croissant',
			': activate to sort column descending'        => ': activer pour trier par ordre décroissant',
			'Detailed Report'                             => 'Rapport détaillé',
			'Score'                                       => 'Score',
			'Order'                                       => 'Ordre',
			/* translators: %s is the quiz title */
			'tablesColumnsAvgQuizScore'                   => 'Score moyen %s',
			/* translators: %s is the course title */
			'tablesColumnsCoursesEnrolled'                => '%s inscrites',
			/* translators: %s is the lesson title */
			'tablesColumnsLessonName'                     => 'Nom %s',
			/* translators: %s is the topic title */
			'tablesColumnsTopicName'                      => 'Nom %s',
			/* translators: %s is the quiz title */
			'tablesColumnsQuizName'                       => 'Nom %s',
			'tablesColumnsDetails'                        => 'Détails',
			'tablesButtonSeeDetails'                      => 'Voir les détails',
			'tablesSearchPlaceholder'                     => 'Rechercher...',
			/* translators: %s is the course title */
			'overviewGoToCourseOverview'                  => 'Vue d\'ensemble %s',
			'overviewGoToCourseUserReport'                => 'Rapport utilisateur',
			'overviewUsers'                               => 'Utilisateurs',
			/* translators: %s is the user ID */
			'overviewUserCardId'                          => 'ID : %s',
			'overviewBoxesTitleRecentActivities'          => 'Activités récentes',
			'overviewBoxesTitleReports'                   => 'Rapports',
			/* translators: %s is the "Courses" label */
			'overviewBoxesTitleCompletedCourses'          => '%s les plus complétées',
			/* translators: %s is the "Courses" label */
			'overviewBoxesReportsCourseReportTitle'       => 'Rapport %s',
			'overviewBoxesReportsCourseReportDescription' => 'Vue d\'ensemble des formations LearnDash et de la progression des apprenants',
			'overviewBoxesReportsUserReportTitle'         => 'Rapport utilisateur',
			'overviewBoxesReportsUserReportDescription'   => 'Suivre la progression des apprenants inscrits aux formations LearnDash',
			'overviewBoxesReportsTinCanReportTitle'       => 'Rapport Tin Can',
			'overviewBoxesReportsTinCanReportDescription' => 'Enregistrements détaillés de l\'activité des apprenants dans H5P et les modules importés',
			/* translators: %s is the "Courses" label */
			'overviewBoxesReportsCoursesCompletionSeeAll' => 'Voir toutes les %s',
			'overviewBoxesReportsCoursesCompletionNoData' => 'Aucune complétion enregistrée',
			'overviewLoading'                             => 'Chargement',
			'graphNoActivity'                             => 'Aucune activité enregistrée',
			'graphNoEnrolledUsers'                        => 'Aucun apprenant inscrit',
			/* translators: %s is the "Courses" label */
			'graphCourseCompletions'                      => 'Complétion de parcours',
			'graphTinCanStatements'                       => 'Complétion de parcours',
			/* translators: %s is the number of completions */
			'graphTooltipCompletions'                     => '%s complétion(s)',
			/* translators: %s is the number of statements */
			'graphTooltipStatements'                      => '%s déclaration(s)',
			'customizeColumns'                            => 'Personnaliser les colonnes',
			'hideCustomizeColumns'                        => 'Masquer la personnalisation des colonnes',
			'showAll'                                     => 'Tout afficher',
			'ID'                                          => 'ID',
		);
	}

	/**
	 * @return array
	 */
	public static function get_i18n_strings() {
		return array(
			'dataNotRight'    => 'Les données sont incorrectes ?',
			/* translators: %s is a link */
			'tryRunning'      => 'Essayez de lancer %s.',
			'updatesLinkText' => 'Mises à niveau des données LearnDash',
			'allQuestions'    => 'Toutes les questions',
			'dropdown'        => array(
				'errorLoading'    => 'Les résultats n\'ont pas pu être chargés.',
				'inputTooLong'    => array(
					'singular' => 'Veuillez supprimer 1 caractère',
					/* translators: %s is a character count */
					'plural'   => 'Veuillez supprimer %s caractères',
				),
				/* translators: %s is a character count */
				'inputTooShort'   => 'Veuillez saisir %s caractères ou plus',
				'loadingMore'     => 'Chargement de plus de résultats...',
				'maximumSelected' => array(
					'singular' => 'Vous ne pouvez sélectionner qu\'1 élément',
					/* translators: %s is a number of items */
					'plural'   => 'Vous ne pouvez sélectionner que %s éléments',
				),
				'noResults'       => 'Aucun résultat trouvé',
				'searching'       => 'Recherche en cours...',
				'removeAllItems'  => 'Supprimer tous les éléments',
			),
			'tables'          => array(
				'processing'        => 'Traitement...',
				'sSearch'           => 'Rechercher',
				'searchPlaceholder' => 'Rechercher',
				/* translators: %s is a number */
				'lengthMenu'        => 'Afficher _MENU_ entrées',
				/* translators: Both %1$s and %2$s are numbers */
				'info'              => 'Page _PAGE_ sur _PAGES_',
				'infoEmpty'         => 'Affichage de 0 à 0 sur 0 entrée',
				/* translators: %s is a number */
				'infoFiltered'      => '(filtré sur _MAX_ entrées au total)',
				'loadingRecords'    => 'Chargement',
				'zeroRecords'       => 'Aucun résultat trouvé',
				'emptyTable'        => 'Aucune donnée disponible dans le tableau',
				'paginate'          => array(
					'first'    => 'Premier',
					'previous' => 'Précédent',
					'next'     => 'Suivant',
					'last'     => 'Dernier',
				),
				'sortAscending'     => ': activer pour trier par ordre croissant',
				'sortDescending'    => ': activer pour trier par ordre décroissant',
				'buttons'           => array(
					'csvExport' => 'Export CSV',
					'pdfExport' => 'Export PDF',
				),
			),
		);
	}
}
