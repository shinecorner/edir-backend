<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::auth();

##
## DB Search
##
Route::get('/ort-suche', 'SearchController@location')->name('location-search');
Route::get('/unterkategorie-suche', 'SearchController@categorySecondary')->name('category-secondary-search');

Route::get('/', 'DashboardController@index');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

//https://github.com/cmgmyr/laravel-messenger/blob/master/src/Cmgmyr/Messenger/examples/routes.php
Route::group(['prefix' => 'messages'], function () {
	Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
	Route::post('/speichern', ['as' => 'messages.store', 'uses' => 'MessagesController@store']);
	Route::get('{slug}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
	Route::get('/oeffnen/{slug}', ['as' => 'messages.open', 'uses' => 'MessagesController@undelete']);
	Route::delete('/loeschen/{slug}', ['as' => 'messages.delete', 'uses' => 'MessagesController@destroy']);
});

##
## Customer access
##
Route::group(['namespace' => 'Customer', 'prefix' => 'kunde'], function () {

	##
	## Customer/Profile
	##
	Route::get('/profil', 'CustomerUserController@form')->name('customer.profile');
	Route::get('profil/bearbeiten', 'CustomerUserController@form')->name('customer.profile.form');
	Route::post('profil/speichern', 'CustomerUserController@store')->name('customer.profile.store');

	##
	## Customer/Company
	##
	Route::get('/firmen', 'CustomerCompanyController@index')->name('customer.company');
	Route::post('/firmen/datatables', 'CustomerCompanyController@datatables')->name('customer.company.datatables');
	Route::get('/firma/bearbeiten/{id?}', 'CustomerCompanyController@form')->name('customer.company.form');
	Route::post('/firma/speichern', 'CustomerCompanyController@store')->name('customer.company.store');
	Route::get('/firmen/loeschen/{id}', 'CustomerCompanyController@delete')->name('customer.company.delete');

	##
	## Customer/Events
	##
	Route::get('/events', 'CustomerEventController@index')->name('customer.event');
	Route::post('/events/datatables', 'CustomerEventController@datatables')->name('customer.event.datatables');
	Route::get('/events/bearbeiten/{id?}', 'CustomerEventController@form')->name('customer.event.form');
	Route::post('/events/speichern', 'CustomerEventController@store')->name('customer.event.store');
	Route::get('/events/loeschen/{id}', 'CustomerEventController@delete')->name('customer.event.delete');

	##
	## Customer/Deals
	##
	Route::get('/deals', 'CustomerDealController@index')->name('customer.deal');
	Route::post('/deals/datatables', 'CustomerDealController@datatables')->name('customer.deal.datatables');
	Route::get('/deals/bearbeiten/{id?}', 'CustomerDealController@form')->name('customer.deal.form');
	Route::post('/deals/speichern', 'CustomerDealController@store')->name('customer.deal.store');
	Route::get('/deals/loeschen/{id}', 'CustomerDealController@delete')->name('customer.deal.delete');

	##
	## Ratings
	##
	Route::get('/ratings', 'CustomerRatingController@index')->name('customer.rating');
	Route::post('/ratings/datatables', 'CustomerRatingController@datatables')->name('customer.rating.datatables');
});

##
## Management
##
Route::group(['middleware' => 'can:manage-directory'], function () {

	##
	## DB Search Admin
	##
	Route::get('/firma-suche', 'SearchController@company')->name('company-search');
	Route::get('/kunden-suche', 'SearchController@customer')->name('customer-search');

	##
	## Audit
	##
	Route::get('/audit', 'AuditController@index')->name('audit');
	Route::post('/audit/datatables', 'AuditController@datatables')->name('audit.datatables');

	##
	## Companies
	##
	Route::get('/firmen', 'CompanyController@index')->name('company');
	Route::post('/firmen/datatables', 'CompanyController@datatables')->name('company.datatables');
	Route::get('/firmen/bearbeiten/{id?}', 'CompanyController@form')->name('company.form');
	Route::post('/firmen/speichern', 'CompanyController@store')->name('company.store');
	Route::get('/firmen/loeschen/{id}', 'CompanyController@delete')->name('company.delete');

	##
	## Users
	##
	Route::get('/benutzer', 'UserController@index')->name('user');
	Route::post('/benutzer/datatables', 'UserController@datatables')->name('user.datatables');
	Route::get('/benutzer/bearbeiten/{id?}', 'UserController@form')->name('user.form');
	Route::post('/benutzer/speichern', 'UserController@store')->name('user.store');
	Route::get('/benutzer/loeschen/{id}', 'UserController@delete')->name('user.delete');


	##
	## Categories
	##
	Route::get('/kategorien/hauptkategorien', 'Categories\CategoryPrimaryController@index')->name('category.primary');
	Route::post('/kategorien/hauptkategorien/datatables', 'Categories\CategoryPrimaryController@datatables')->name('category.primary.datatables');
	Route::get('/kategorien/hauptkategorien/bearbeiten/{id?}', 'Categories\CategoryPrimaryController@form')->name('category.primary.form');
	Route::post('/kategorien/hauptkategorien/speichern', 'Categories\CategoryPrimaryController@store')->name('category.primary.store');
	Route::get('/kategorien/hauptkategorien/loeschen/{id}', 'Categories\CategoryPrimaryController@delete')->name('category.primary.delete');

	Route::get('/kategorien/unterkategorien', 'Categories\CategorySecondaryController@index')->name('category.secondary');
	Route::post('/kategorien/unterkategorien/datatables', 'Categories\CategorySecondaryController@datatables')->name('category.secondary.datatables');
	Route::get('/kategorien/unterkategorien/bearbeiten/{id?}', 'Categories\CategorySecondaryController@form')->name('category.secondary.form');
	Route::post('/kategorien/unterkategorien/speichern', 'Categories\CategorySecondaryController@store')->name('category.secondary.store');
	Route::get('/kategorien/unterkategorien/loeschen/{id}', 'Categories\CategorySecondaryController@delete')->name('category.secondary.delete');

	Route::get('/kategorien/eventkategorien', 'Categories\CategoryEventController@index')->name('category.event');
	Route::post('/kategorien/eventkategorien/datatables', 'Categories\CategoryEventController@datatables')->name('category.event.datatables');
	Route::get('/kategorien/eventkategorien/bearbeiten/{id?}', 'Categories\CategoryEventController@form')->name('category.event.form');
	Route::post('/kategorien/eventkategorien/speichern', 'Categories\CategoryEventController@store')->name('category.event.store');
	Route::get('/kategorien/eventkategorien/loeschen/{id}', 'Categories\CategoryEventController@delete')->name('category.event.delete');

	Route::get('/kategorien/dealkategorien', 'Categories\CategoryDealController@index')->name('category.deal');
	Route::post('/kategorien/dealkategorien/datatables', 'Categories\CategoryDealController@datatables')->name('category.deal.datatables');
	Route::get('/kategorien/dealkategorien/bearbeiten/{id?}', 'Categories\CategoryDealController@form')->name('category.deal.form');
	Route::post('/kategorien/dealkategorien/speichern', 'Categories\CategoryDealController@store')->name('category.deal.store');
	Route::get('/kategorien/dealkategorien/loeschen/{id}', 'Categories\CategoryDealController@delete')->name('category.deal.delete');

	##
	## Events
	##
	Route::get('/events', 'EventController@index')->name('event');
	Route::post('/events/datatables', 'EventController@datatables')->name('event.datatables');
	Route::get('/events/bearbeiten/{id?}', 'EventController@form')->name('event.form');
	Route::post('/events/speichern', 'EventController@store')->name('event.store');
	Route::get('/events/loeschen/{id}', 'EventController@delete')->name('event.delete');

	##
	## Deals
	##
	Route::get('/deals', 'DealController@index')->name('deal');
	Route::post('/deals/datatables', 'DealController@datatables')->name('deal.datatables');
	Route::get('/deals/bearbeiten/{id?}', 'DealController@form')->name('deal.form');
	Route::post('/deals/speichern', 'DealController@store')->name('deal.store');
	Route::get('/deals/loeschen/{id}', 'DealController@delete')->name('deal.delete');

	##
	## Ratings
	##
	Route::get('/ratings', 'RatingController@index')->name('rating');
	Route::post('/ratings/datatables', 'RatingController@datatables')->name('rating.datatables');
	Route::get('/ratings/bearbeiten/{id?}', 'RatingController@form')->name('rating.form');
	Route::post('/ratings/speichern', 'RatingController@store')->name('rating.store');
	Route::get('/ratings/loeschen/{id}', 'RatingController@delete')->name('rating.delete');

	##
	## Blog
	##
	Route::get('/blog', 'BlogPostController@index')->name('blog');
	Route::post('/blog/datatables', 'BlogPostController@datatables')->name('blog.datatables');
	Route::get('/blog/bearbeiten/{id?}', 'BlogPostController@form')->name('blog.form');
	Route::post('/blog/speichern', 'BlogPostController@store')->name('blog.store');
	Route::get('/blog/loeschen/{id}', 'BlogPostController@delete')->name('blog.delete');

	##
	## Directory
	##
	Route::get('/directory', 'DirectoryController@index')->name('directory');
	Route::post('/directory/datatables', 'DirectoryController@datatables')->name('directory.datatables');
	Route::get('/directory/bearbeiten/{id?}', 'DirectoryController@form')->name('directory.form');
	Route::post('/directory/speichern', 'DirectoryController@store')->name('directory.store');
	Route::get('/directory/loeschen/{id}', 'DirectoryController@delete')->name('directory.delete');

});