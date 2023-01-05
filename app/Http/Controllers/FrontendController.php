<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $home_page_variant = get_home_variant();
        $lang = LanguageHelper::user_lang_slug();
        //make a function to call all static option by home page
        $static_field_data = StaticOption::whereIn('option_name',HomePageStaticSettings::get_home_field(get_static_option('home_page_variant')))->get()->mapWithKeys(function ($item) {
            return [$item->option_name => $item->option_value];
        })->toArray();
        if (!empty(get_static_option('home_page_page_builder_status'))){
            return view('frontend.frontend-home')->with([ 'static_field_data' => $static_field_data]);
        }

        $all_header_slider = HeaderSlider::where('lang', $lang)->get();
        $all_counterup = Counterup::where('lang', $lang)->get();
        $all_key_features = KeyFeatures::where('lang', $lang)->get();
        $all_service = Services::where(['lang' => $lang, 'status' => 'publish'])->orderBy('sr_order', 'ASC')->take(get_static_option('home_page_01_service_area_items'))->get();
        $all_testimonial = Testimonial::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'desc')->get();
        $all_price_plan = PricePlan::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'desc')->take(get_static_option('home_page_01_price_plan_section_items'))->get();
        $all_team_members = TeamMember::where('lang', $lang)->orderBy('id', 'desc')->take(get_static_option('home_page_01_team_member_items'))->get();
        $all_brand_logo = Brand::all();
        $all_work = Works::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'desc')->take(get_static_option('home_page_01_case_study_items'))->get();
        $all_blog = Blog::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'desc')->take(6)->get();
        $all_contact_info = ContactInfoItem::where(['lang' => $lang])->orderBy('id', 'desc')->get();
        $all_service_category = ServiceCategory::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'desc')->take(get_static_option('home_page_01_service_area_items'))->get();
        $all_contain_cat = $all_work->map(function ($index) { return $index->categories_id; });
        $works_cat_ids = [];
        foreach($all_contain_cat as $k=>$v){
            foreach($v as $key=>$value){
                if(!in_array($value, $works_cat_ids)){
                    $works_cat_ids[]=$value;
                }
            }
        }
        $all_work_category = WorksCategory::find($works_cat_ids);


        $blade_data = [
            'static_field_data' => $static_field_data,
            'all_header_slider' => $all_header_slider,
            'all_counterup' => $all_counterup,
            'all_key_features' => $all_key_features,
            'all_service' => $all_service,
            'all_testimonial' => $all_testimonial,
            'all_blog' => $all_blog,
            'all_price_plan' => $all_price_plan,
            'all_team_members' => $all_team_members,
            'all_brand_logo' => $all_brand_logo,
            'all_work_category' => $all_work_category,
            'all_work' => $all_work,
            'all_service_category' => $all_service_category,
            'all_contact_info' => $all_contact_info,
        ];

        if (in_array($home_page_variant,['10','12','16']) ){
            //appointment module for home page 10,12,16
            $appointment_query = Appointment::query();
            $appointment_query->with('lang_front');
            $feature_product_list = get_static_option('home_page_' . get_static_option('home_page_variant') . '_appointment_section_category') ?? '';
            $feature_product_list = unserialize($feature_product_list, ['class' => false]);
            if (is_array($feature_product_list) && count($feature_product_list) > 0) {
                $appointment_query->whereIn('categories_id', $feature_product_list);
            }
            $appointments = $appointment_query->get();
            $blade_data['appointments'] = $appointments;
        }

        if ($home_page_variant == '20'){
            $breaking_news =  Blog::where(['lang' => $lang, 'status' => 'publish','breaking_news' => 1])->orderBy('id', 'desc')->take(12)->get();
            $blade_data['breaking_news'] = $breaking_news;
            $blade_data['header_slider_item'] =  Blog::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'desc')->take(get_static_option('home20_header_section_items',5))->get();

           //advertisement code top section
            $advertisement_type = get_static_option('home_page_newspaper_advertisement_type');
            $advertisement_size = get_static_option('home_page_newspaper_advertisement_size');
            $add_query = Advertisement::select('id','type','image','slot','status','redirect_url','embed_code','title');
            if (!empty($advertisement_type)){
                $add_query = $add_query->where('type',$advertisement_type);
            }
            if (!empty($advertisement_size)){
                $add_query = $add_query->where('size',$advertisement_size);
            }
            $add = $add_query->where('status',1)->inRandomOrder()->first();
            $blade_data['add_id'] = $add->id;

            $image_markup = render_image_markup_by_attachment_id($add->image,null,'full');
            $redirect_url = $add->redirect_url;
            $slot = $add->slot;
            $embed_code = $add->embed_code;

            $blade_data['advertisement_markup'] = '';
            if ($add->type === 'image'){
                $blade_data['advertisement_markup'].= '<a href="'.$redirect_url.'">'.$image_markup.'</a>';
            }elseif($add->type === 'google_adsense'){
                $blade_data['advertisement_markup'].= $this->script_add($slot);
            }else{
                $blade_data['advertisement_markup'].= '<div>'.$embed_code.'</div>';
            }
           //advertisement code top section


            //advertisement code bottom section
            $advertisement_type = get_static_option('home_page_newspaper_advertisement_type_bottom');
            $advertisement_size = get_static_option('home_page_newspaper_advertisement_size_bottom');
            $add_query = Advertisement::select('id','type','image','slot','status','redirect_url','embed_code','title');
            if (!empty($advertisement_type)){
                $add_query = $add_query->where('type',$advertisement_type);
            }
            if (!empty($advertisement_size)){
                $add_query = $add_query->where('size',$advertisement_size);
            }
            $add = $add_query->where('status',1)->inRandomOrder()->first();
            $blade_data['add_id'] = $add->id;

            $image_markup = render_image_markup_by_attachment_id($add->image,null,'full');
            $redirect_url = $add->redirect_url;
            $slot = $add->slot;
            $embed_code = $add->embed_code;

            $blade_data['advertisement_markup_bottom'] = '';
            if ($add->type === 'image'){
                $blade_data['advertisement_markup_bottom'].= '<a href="'.$redirect_url.'">'.$image_markup.'</a>';
            }elseif($add->type === 'google_adsense'){
                $blade_data['advertisement_markup_bottom'].= $this->script_add($slot);
            }else{
                $blade_data['advertisement_markup_bottom'].= '<div>'.$embed_code.'</div>';
            }
            //advertisement code bottom section

            $popular_categories_id = json_decode(get_static_option('home20_popular_news_section_'.$lang.'_categories'));
            $popular_categories = BlogCategory::where(['status' => 'publish','lang' => $lang])->whereIn('id',$popular_categories_id)->get();
            $blade_data['popular_categories'] = $popular_categories;
            $video_news_items = Blog::where(['status' => 'publish','lang' => $lang])->whereNotNull('video_url')->take(get_static_option('home20_video_news_section_items',4))->get();
            
            
            $blade_data['video_news_items'] = $video_news_items;

            $sport_news_categories_id = json_decode(get_static_option('home20_sports_news_section_'.$lang.'_categories'));
            $sports_news_item = Blog::where(['status' => 'publish','lang' => $lang])->whereIn('blog_categories_id',$sport_news_categories_id)->take(get_static_option('home20_sports_news_section_items',5))->get();
            $blade_data['sports_news_item'] = $sports_news_item;

            $hot_news_categories_id = json_decode(get_static_option('home20_hot_news_section_'.$lang.'_categories'));
            $hot_news_item = Blog::where(['status' => 'publish','lang' => $lang])->whereIn('blog_categories_id',$hot_news_categories_id)->take(get_static_option('home20_hot_news_section_items',5))->get();
            $blade_data['hot_news_item'] = $hot_news_item;
        }

        if ($home_page_variant == '13'){
            //popular donation cause
            $popular_cause_query = Donation::query();
            $popular_cause_list = get_static_option('home_page_13_' . $lang . '_popular_cause_popular_cause_list') ??  serialize([]);
            $popular_cause_list = unserialize($popular_cause_list, ['class' => false]);
            $popular_cause_items = get_static_option('home_page_13_popular_cause_popular_cause_items') ?? '';
            $popular_cause_order = get_static_option('home_page_13_popular_cause_popular_cause_order') ?? 'asc';
            $popular_cause_orderby = get_static_option('home_page_13_popular_cause_popular_cause_orderby') ?? 'id';

            if (count($popular_cause_list) > 0) {
                $popular_cause_query->whereIn('id', $popular_cause_list);
            }

            $popular_causes = $popular_cause_query->where('lang', $lang)->orderBy($popular_cause_orderby, $popular_cause_order)->take($popular_cause_items)->get();
            $blade_data['popular_causes'] = $popular_causes;
        }

        if (in_array($home_page_variant,['13','15','17','18'])){
            $all_events = Events::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'DESC')->take(get_static_option('home_page_01_event_area_items'))->get();
            $latest_products = Products::where(['lang' => $lang, 'status' => 'publish'])->orderBy('id', 'DESC')->take(get_static_option('home_page_products_area_items'))->get();
            $blade_data['all_events'] = $all_events;
            $blade_data['latest_products'] = $latest_products;
        }
        if (in_array($home_page_variant,['15','18'])){
            $product_query = Products::query();
            $feature_product_list = get_static_option('home_page_15_' . $lang . '_featured_product_area_items') ??  serialize([]);
            $feature_product_list = unserialize($feature_product_list, ['class' => false]);
            if (count($feature_product_list) > 0) {
                $product_query->whereIn('id', $feature_product_list);
            }
            $featured_products = $product_query->where('lang', $lang)->get();

            //best selling product
            $top_selling_products = Products::where(['lang' => $lang, 'status' => 'publish'])->orderBy('sales', 'desc')->take(get_static_option('home_page_15_top_selling_product_area_items'))->get();
            $blade_data['featured_products'] = $featured_products;
            $blade_data['top_selling_products'] = $top_selling_products;
        }


        if (in_array($home_page_variant,['17'])){
            //courses category
            $all_courses_category = CoursesCategory::where(['status' => 'publish'])->get();
            //
            $featured_courses_ids = unserialize(get_static_option('featured_courses_ids'), ['class' => false]); //fetch featured courses from db by admin selected ids;
            $featured_courses = Course::with('lang_front')->whereIn('id', $featured_courses_ids)->get(); //fetch featured courses from db by admin selected ids;
            //
            $latest_courses = Course::with('lang_front')->get()->take(get_static_option('course_home_page_all_course_area_items')); //get all latest course items, limit by admin given limit;

            $blade_data['latest_courses'] = $latest_courses;
            $blade_data['featured_courses'] = $featured_courses;
            $blade_data['all_courses_category'] = $all_courses_category;
        }

        if (in_array($home_page_variant,['18'])){
            //product categories
            $product_categories = ProductCategory::where(['lang' => $lang, 'status' => 'publish'])->get();
            $blade_data['product_categories'] = $product_categories;
        }

        if (in_array($home_page_variant,['19'])){
            //hot deal section products
             $selected_products = json_decode(get_static_option('home_page_19_'.get_user_lang().'_todays_deal_products')) ?? [];
             $hot_deal_pro = Products::with("ratings")
                 ->withCount('ratings')
                 ->whereIn('id',$selected_products)->where(['lang' => $lang, 'status' => 'publish'])->get();
             $blade_data['all_hot_deal_products'] = $hot_deal_pro;

            //store area section products
            $selected_categories = json_decode(get_static_option('home19_store_section_'.get_user_lang().'_categories')) ?? [];
            $store_area_categories = ProductCategory::whereIn('id',$selected_categories)->where(['lang' => $lang, 'status' => 'publish'])->take(get_static_option('home19_store_section_category_items'))->get();
            $blade_data['all_store_area_categories'] = $store_area_categories;

            //Popular section products
            $selected_popular_products = json_decode(get_static_option('home_page_19_'.get_user_lang().'_popular_area_products')) ?? [];
            $all_popular_products = Products::with("ratings")
                ->withCount('ratings')
                ->whereIn('id',$selected_popular_products)->where(['lang' => $lang, 'status' => 'publish'])->get();
                 $blade_data['all_popular_products'] = $all_popular_products;

             //Instagram Section
            $post_items = get_static_option('home_page_19_instagram_area_item_show');
            $instagram_data = Cache::remember('instagram_feed',now()->addDay(2),function () use($post_items) {
                $insta_data = InstagramFeed::fetch($post_items);
                return $insta_data ?? [];
            });

            if (!$instagram_data) {
               // return '';
            }
            $blade_data['all_instagram_data'] = $instagram_data;
            $pro_cat = ProductCategory::with('subcategory')->where(['lang' => $lang, 'status' => 'publish'])->get();
            $blade_data['product_categories_for_sidebar'] = $pro_cat;
        }

        return view('frontend.frontend-home')->with($blade_data);
    }

    public function lang_change(Request $request)
    {
        session()->put('lang', $request->lang);
        return redirect()->route('homepage');
    }


    public function services_single_page($slug)
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $service_item = Services::where('slug', $slug)->first();
        if (empty($service_item)){
            abort(404);
        }
        $service_category = ServiceCategory::where(['status' => 'publish', 'lang' => $lang])->get();
        $price_plan = !empty($service_item) && !empty($service_item->price_plan) ? PricePlan::find(unserialize($service_item->price_plan)) : '';
        return view('frontend.pages.service.service-single')->with(['service_item' => $service_item, 'service_category' => $service_category, 'price_plan' => $price_plan]);
    }


    public function category_wise_services_page($id, $any)
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $category_name = ServiceCategory::find($id)->name;
        if(empty($category_name)){
            abort('404');
        }
        $service_item = Services::where(['categories_id' => $id, 'lang' => $lang])->paginate(6);
        return view('frontend.pages.service.service-category')->with(['service_items' => $service_item, 'category_name' => $category_name]);
    }


    public function work_single_page($slug)
    {
        $work_item = Works::where('slug', $slug)->first();
        if (empty($work_item)){
            abort(404);
        }
        $all_works = [];
        $all_related_works = [];
        foreach ($work_item->categories_id as $cat) {
            $all_by_cat = Works::where(['lang' => get_user_lang()])->where('categories_id', 'LIKE', '%' . $work_item->$cat . '%')->take(6)->get();
            for ($i = 0; $i < count($all_by_cat); $i++) {
                array_push($all_works, $all_by_cat[$i]);
            }
        }
        array_unique($all_works);
        return view('frontend.pages.work.work-single')->with(['work_item' => $work_item, 'related_works' => $all_works]);
    }


    public function about_page()
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $all_brand_logo = Brand::all();
        $all_team_members = TeamMember::where('lang', $lang)->orderBy('id', 'desc')->take(get_static_option('about_page_team_member_item'))->get();
        $all_testimonial = Testimonial::where('lang', $lang)->orderBy('id', 'desc')->take(get_static_option('about_page_testimonial_item'))->get();
        $all_key_features = KeyFeatures::where('lang', $lang)->get();
        return view('frontend.pages.about')->with([
            'all_brand_logo' => $all_brand_logo,
            'all_team_members' => $all_team_members,
            'all_testimonial' => $all_testimonial,
            'all_key_features' => $all_key_features,
        ]);
    }


    public function service_page()
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $all_services = Services::where('lang', $lang)->orderBy('sr_order', 'asc')->paginate(get_static_option('service_page_service_items'));
        return view('frontend.pages.service.services')->with(['all_services' => $all_services]);
    }


    public function work_page()
    {
        $default_lang = Language::where('default', 1)->first();
       
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
         
        $all_work = Works::where(['lang' => $lang])->orderBy('id', 'desc')->paginate(12);
        
        $all_contain_cat = [];
        foreach ($all_work as $work) {
            $all_contain_cat[] = $work->categories_id;
        }
        $all_work_category = WorksCategory::find(array_unique(array_flatten($all_contain_cat)));

        return view('frontend.pages.work.work')->with(['all_work' => $all_work, 'all_work_category' => $all_work_category]);
    }
    

    public function team_page()
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $all_team_members = TeamMember::where('lang', $lang)->orderBy('id', 'desc')->paginate(12);

        return view('frontend.pages.team-page')->with(['all_team_members' => $all_team_members]);
    }


    public function faq_page()
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $all_faq = Faq::where(['lang' => $lang, 'status' => 'publish'])->get();
        return view('frontend.pages.faq-page')->with([
            'all_faqs' => $all_faq
        ]);
    }

    
    public function contact_page()
    {
        $default_lang = Language::where('default', 1)->first();
        $lang = !empty(session()->get('lang')) ? session()->get('lang') : $default_lang->slug;
        $all_contact_info = ContactInfoItem::where('lang', $lang)->get();
        return view('frontend.pages.contact-page')->with([
            'all_contact_info' => $all_contact_info
        ]);
    }

}
