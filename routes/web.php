<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});



Route::group(['middleware' => ['setlang:frontend', 'globalVariable', 'maintains_mode', 'HtmlMinifier']], function () {

    Route::get('/', 'FrontendController@index')->name('homepage');


//static page
$about_page_slug = get_static_option('about_page_slug') ?? 'about';
$work_page_slug = get_static_option('work_page_slug') ?? 'work';
$faq_page_slug = get_static_option('faq_page_slug') ?? 'faq';
$team_page_slug = get_static_option('team_page_slug') ?? 'team';
$price_plan_page_slug = get_static_option('price_plan_page_slug') ?? 'price-plan';
$contact_page_slug = get_static_option('contact_page_slug') ?? 'contact';
$blog_page_slug = get_static_option('blog_page_slug') ?? 'blog';
$quote_page_slug = get_static_option('quote_page_slug') ?? 'request-quote';
$testimonial_page_slug = get_static_option('testimonial_page_slug') ?? 'testimonials';
$feedback_page_slug = get_static_option('feedback_page_slug') ?? 'feedback';
$clients_feedback_page_slug = get_static_option('clients_feedback_page_slug') ?? 'clients-feedback';
$image_gallery_page_slug = get_static_option('image_gallery_page_slug') ?? 'image-gallery';
$video_gallery_page_slug = get_static_option('video_gallery_page_slug') ?? 'video-gallery';
$donor_page_slug = get_static_option('donor_page_slug') ?? 'donor-list';

  
       // FRONTEND: SERVICES ROUTES
  
    $service_page_slug = get_static_option('service_page_slug') ?? 'service';
    Route::get($service_page_slug, 'FrontendController@service_page')->name('frontend.service');
    Route::get($service_page_slug.'/category/{id}/{any?}', 'FrontendController@category_wise_services_page')->name('frontend.services.category');
    Route::get( $service_page_slug.'/{slug}', 'FrontendController@services_single_page')->name('frontend.services.single');


   
       // FRONTEND: ROUTES
    
    Route::get('/' . $testimonial_page_slug, 'FrontendController@testimonials')->name('frontend.testimonials');
    Route::get('/' . $feedback_page_slug, 'FrontendController@feedback_page')->name('frontend.feedback');
    Route::get('/' . $clients_feedback_page_slug, 'FrontendController@clients_feedback_page')->name('frontend.clients.feedback');
    Route::post('/' . $clients_feedback_page_slug . '/submit', 'FrontendFormController@clients_feedback_store')->name('frontend.clients.feedback.store');
    Route::get('/' . $image_gallery_page_slug . '', 'FrontendController@image_gallery_page')->name('frontend.image.gallery');
    Route::get('/' . $price_plan_page_slug . '/{id}', 'FrontendController@plan_order')->name('frontend.plan.order');
    Route::get('/' . $video_gallery_page_slug . '', 'FrontendController@video_gallery_page')->name('frontend.video.gallery');


    
    Route::post('/get-touch', 'FrontendFormController@get_touch')->name('frontend.get.touch');
    Route::post('/appointment-message', 'FrontendFormController@appointment_message')->name('frontend.appointment.message');
    Route::post('/service-quote', 'FrontendFormController@service_quote')->name('frontend.service.quote');
    Route::post('/case-study-quote', 'FrontendFormController@case_study_quote')->name('frontend.case.study.quote');
   
      //  SUBSCRIBER VERIFY
   
    Route::get('/subscriber/email-verify/{token}','FrontendController@subscriber_verify')->name('subscriber.verify');


    //user login
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('user.login');
    Route::post('/ajax-login', 'FrontendController@ajax_login')->name('user.ajax.login');
    Route::post('/login', 'Auth\LoginController@login');
    Route::get('/register', 'Auth\RegisterController@showRegistrationForm')->name('user.register');
    Route::post('/register', 'Auth\RegisterController@register');
    Route::get('/login/forget-password', 'FrontendController@showUserForgetPasswordForm')->name('user.forget.password');
    Route::get('/login/reset-password/{user}/{token}', 'FrontendController@showUserResetPasswordForm')->name('user.reset.password');
    Route::post('/login/reset-password', 'FrontendController@UserResetPassword')->name('user.reset.password.change');
    Route::post('/login/forget-password', 'FrontendController@sendUserForgetPasswordMail');
    Route::post('/logout', 'Auth\LoginController@logout')->name('user.logout');
    //user email verify
    Route::get('/user/email-verify', 'UserDashboardController@user_email_verify_index')->name('user.email.verify');
    Route::get('/user/resend-verify-code', 'UserDashboardController@reset_user_email_verify_code')->name('user.resend.verify.mail');
    Route::post('/user/email-verify', 'UserDashboardController@user_email_verify');

    Route::post('/request-quote', 'FrontendFormController@send_quote_message')->name('frontend.quote.message');
    Route::post('/request-estimate', 'FrontendFormController@send_estimate_message')->name('frontend.estimate.message');
    Route::get('/home/{id}', 'FrontendController@home_page_change')->name('frontend.homepage.demo');

});




Route::group(['middleware' => ['setlang:frontend', 'globalVariable', 'HtmlMinifier']], function () {
    Route::get('/{slug}', 'FrontendController@dynamic_single_page')->name('frontend.dynamic.page');
});
