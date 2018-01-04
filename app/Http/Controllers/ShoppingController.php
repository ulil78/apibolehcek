<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Exceptions\JWTException;

use Illuminate\Http\Request;
use JWTAuth;
use DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use Cart;
use App\Products;



class ShoppingController extends Controller
{

    public function __construct()
    {
        if(!Auth::check()) {
             $this->middleware('auth',['only'=>'getCheckout']);
        }
    }

    public function getAddToCart()
    {
        Cart::add(
                    '293ad',    //id product
                    'Tas',      //Nama Product
                     1,         //Qty
                     250000,    //Harga
                     array('ukuran' => '10Liter', 'warna' => 'Hitam') //option
                );
    }
    public function getAddItem($id) //kalau di url bentuknya jadi /shop/add-item
    {
      $product = Product::find($id);

      $berat = $product->weight / 1000;


        Cart::add(
                $product->id,
                $product->name,
                 1,
                 $product->price,
                 $berat,
                );
        return redirect('/shop/cart-content');
    }

    public function getCartContent()
    {
        $cart_content = Cart::content();

        return view('frontend/cart-content')->with('cart_content', $cart_content)
                                            ->with('page_title', 'Shoping Cart Content')
                                            ->with('profiles', $profiles)
                                            ->with('latesnews', $latesnews)
                                            ->with('trf', $trf)
                                            ->with('categories', $categories)
                                            ->with('client', $client);



    }

    public function postCoupon(Request $request)
    {

        $id = $request->input('code_coupon');
        $coupon = \App\Coupons::where('code', $id)->first();


        $count = \App\Coupons::where('code', $id)->count();

        $limit = \App\InvoiceDetail::where('product_branchs_id', $id)->count();

         $user_coupon = \DB::table('coupons')
                            ->join('invoices', 'invoices.coupons_id', '=', 'coupons.id')
                            ->select('invoices.coupons_id', 'coupons.code as code', 'invoices.users_id as users_id')
                            ->where('coupons.code', $id)
                            ->where('invoices.users_id', \Auth::user()->id)
                            ->count();

        if($count < 1) {
            Session::flash('message','Kode Kupon tidak ditemukan');
            return redirect('/shop/shipping-detail-confirm');
        }elseif($user_coupon > 0){
            Session::flash('message','Kode Kupon sudah pernah di gunakan');
            return redirect('/shop/shipping-detail-confirm');

        }else{
            $today = new \DateTime(date('Y-m-d H:i:s'));
            if ($today < $coupon->expire_date)
            {
                Session::flash('message','Kupon Kadaluarsa');
                return redirect('/shop/shipping-detail-confirm');
            }elseif(Cart::total() < $coupon->minimum_payment){
                Session::flash('message','Total belanja kurang dari minimum belanja kupon, tambahkan keranjang belanja anda');
                return redirect('/shop/shipping-detail-confirm');


            }elseif($limit > $coupon->limit_transaction){
                Session::flash('message','Quota Kupon sudah habis');
                return redirect('/shop/shipping-detail-confirm');
            }elseif(strlen(Session::get('coupon')) >= 1){
                Session::flash('message','Kupon telah digunakan');
                return redirect('/shop/shipping-detail-confirm');
            }else{

                Session::put('coupons_id', $coupon->id);
                Session::put('coupon', -$coupon->discounts);
                return redirect('/shop/shipping-detail-confirm');
            }

        }

    }


    public function getShippingDetail()
    {

       $cart_content = Cart::content();
        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();


        return view('frontend/shipping-detail')->with('page_title', 'Shipping Detail')
                                               ->with('users', $users)
                                               ->with('cart_content', $cart_content)
                                                ->with('profiles', $profiles)
                                                ->with('latesnews', $latesnews)
                                                ->with('trf', $trf)
                                                ->with('categories', $categories)
                                                ->with('locations', $locations)
                                                ->with('client', $client);
    }


    public function postShippingDetail(Request $request)
    {
        $rules = array(
                    'alamat'        => 'required',
                    'provinsi'      => 'required',
                    'kota'          => 'required',
                    'kodepos'       => 'required',
                    'telepon'       => 'required',
                    'notice'        => ''

            );

       $this->validate($request, $rules);

        $provinsi = $request->input('provinsi');


        $prov = \DB::table('shipping_rates')->where('destination', $provinsi)->count();

        if($prov == 0) {

             Session::flash('message',' - Nama Kecamatan Kosong atau tidak  terdaftar, ulangi lagi!');
            return redirect('/shop/shipping-detail');


        }else{

           $user = \App\User::find(Auth::user()->id);
           $user->alamat = $request->input('alamat');
           $user->kota   =  $request->input('kota');
           $user->provinsi = $request->input('provinsi');
           $user->kodepos = $request->input('kodepos');
           $user->telepon = $request->input('telepon');
           $user->save();

           return redirect('/shop/shipping-detail-confirm');
        }
    }


    public function getShippingDetailConfirm()
    {


  $cart_content = Cart::content();


        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();


        return view('frontend/shipping-detail-confirm')->with('page_title', 'Shipping Detail')
                                               ->with('users', $users)
                                               ->with('cart_content', $cart_content)

                                                ->with('profiles', $profiles)
                                                ->with('latesnews', $latesnews)
                                                ->with('trf', $trf)
                                                ->with('categories', $categories)
                                                ->with('locations', $locations)
                                                ->with('client', $client);


    }
    public function postShippingDetailConfirm(Request $request)
    {

       Session::put('alamat', $request->input('alamat'));
       Session::put('kota',    $request->input('kota'));
       Session::put('provinsi', $request->input('provinsi'));
       Session::put('kodepos', $request->input('kodepos'));
       Session::put('telepon', $request->input('telepon'));
        if ($request->has('notice')){
            Session::put('notice', $request->input('notice'));
        }
        Session::put('weight_amount', $request->input('weight_amount'));
        Session::put('grand_total', $request->input('grand_total'));

       $cara_bayar = $request->input('cara_bayar') ;
        if($cara_bayar == 'transfer')
        {
            return redirect('/shop/process-checkout');
        }else{
            if(Session::get('grand_total') < 50000 && \Auth::user()->id <> 14)
            {
                Session::flash('message','Pembayaran dengan Kartu Kredit harus lebih besar dari Rp. 50.000.-');
                return redirect('/shop/shipping-detail-confirm');

            }else{
                return redirect('/shop/pay-with-veritrans');
            }
        }
    }

    public function getCheckout()
    {

        $cart_content = Cart::content();


        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();




            return redirect('/shop/shipping-detail')->with('page_title', 'Shipping Detail')
                                               ->with('users', $users)
                                               ->with('cart_content', $cart_content)

                                                ->with('profiles', $profiles)
                                                ->with('latesnews', $latesnews)
                                                ->with('trf', $trf)
                                                ->with('categories', $categories)
                                                ->with('locations', $locations)
                                                ->with('client', $client);


    }

    public function getProcessCheckout()
    {
        // insert ke table invoice
        $today = new \DateTime(date('Y-m-d H:i:s'));
        $tomorrow = $today->modify('+1 day');

        $invoice = new Invoice;
        $invoice->order_id      = rand();

            $content = Cart::content();
            foreach($content as $con)
            {
                $branchlast = $con->branchs_id;
            }

        $invoice->branchs_id    = $branchlast;
        $invoice->users_id       = Auth::user()->id;
        $invoice->due_date      = $tomorrow;
        $invoice->sub_total     = Cart::total();
        $invoice->total_qty     = Cart::count();
        $invoice->weight_total  = Cart::totalberat();
        $invoice->weight_amount = Session::get('weight_amount');
        $invoice->total_discount = Cart::totaldiskon();
        if(Session::has('coupons_id')){
            $invoice->coupons_id = Session::pull('coupons_id');
            $invoice->coupon_disc = Session::pull('coupon');
        }

        $invoice->total_amount  = Session::get('grand_total');
        if(Session::has('notice')) {
            $invoice->notice  = Session::pull('notice');
        }
        $invoice->status        = 'unpaid';
        $invoice->save();

        if($invoice->coupons_id > 0){
            $id_coupon = $invoice->coupons_id;
            $coupon_counter = \App\Coupons::where('id', $id_coupon)->value('counter');

            $counter = $coupon_counter + 1;

            $coupon = DB::table('coupons')
                            ->where('id', $id_coupon)
                            ->update(['counter' => $counter]);


        }

         //insert ke invoice datail  setat invoice di save pagil invoice id untuk detail = $invoice->id;

        $cart_content = Cart::content();

        foreach ($cart_content as $item) {
            $invoice_detail = new InvoiceDetail;
            $invoice_detail->invoices_id        =  $invoice->id;
            $invoice_detail->product_branchs_id =  $item->id;
            $invoice_detail->branchs_id         =  $item->branchs_id;

            $invoice_detail->qty                =  $item->qty;
            $invoice_detail->weight              =  $item->weight;

            //if($item->sales_price == 0)
            //{
              $invoice_detail->price              =  $item->price;
            //}else{
              // $invoice_detail->price              =  $item->sales_price;
            //}

            $disc1 = $item->price - $item->sales_price;
            $disc2 = $disc1 == 0 ? 0 : ($disc1 / $item->price)*100;

            $disc3 = $disc1 == $item->price ? 0 : ($disc1 / $item->price)*100 ;


            $invoice_detail->discount             =  $disc3;
            $invoice_detail->subtotal           =  $item->subtotal;
            $invoice_detail->save();


            //stock ajustemt start

            $start_stock = \App\ProductBranchs::where('id', '=', $item->id)->value('stock');

            $end_stock = $start_stock - $item->qty;

            $product_id = \App\ProductBranchs::where('id', '=', $item->id)->value('products_id');


            $productadjustment                            = new \App\ProductAdjustments;
            $productadjustment->adjustment_types_id       = 2;
            $productadjustment->branchs_id                = $item->branchs_id;
            $productadjustment->products_id               = $product_id;
            $productadjustment->start_stock               = $start_stock;
            $productadjustment->qty                       = -$item->qty;
            $productadjustment->end_stock                 = $end_stock;
            $productadjustment->price                     = $item->price;
            $productadjustment->sales_price               = $item->sales_price;
            $productadjustment->status                    = 'publish';
            $productadjustment->users_id                  = \Auth::user()->id;
            $productadjustment->save();

            \DB::table('product_branchs')
                        ->where('branchs_id', $item->branchs_id)
                        ->where('id', $item->id)
                        ->update(['stock' => $end_stock]);


            //end stock adjustment


        }


        $shipping_invoice = new \App\ShippingInvoices;
        $shipping_invoice->order_id = $invoice->order_id;
        $shipping_invoice->shipping_amount = $invoice->weight_amount;
        $shipping_invoice->save();


        $banks = \App\Banks::all();

        $tgl1 = $invoice->due_date;
        $due_date = $tgl1->format('d M Y H:i:s');

        $hariini = new \DateTime(date('Y-m-d H:i:s'));
        $tgl = $hariini->format('d M Y H:i:s');

        //kirim email notif ke customer
        $user = Auth::user();
        Mail::send('mail/checkout-customer',
                    ['invoice' => $invoice, 'banks' => $banks, 'due_date' => $due_date, 'tgl' => $tgl],
                    function ($m) use ($user, $invoice) {
                    $m->to($user->email, $user->fullname)
                    ->subject('Invoice #'.$invoice->id);

           });



        //kirim email notif ke admin
        Mail::send('mail/checkout-admin',
                    ['invoice' => $invoice, 'user' => $user, 'due_date' => $due_date, 'tgl' => $tgl],
                    function ($m) {
                    $m->to('apotekmart.online@gmail.com', 'Admin - Apotekmart')
                    ->subject('New Order');
           });


        Cart::destroy();
       Session::forget('coupon');
       Session::forget('coupons_id');

        return redirect('/shop/checkout-success');
    }

    public function getCheckoutSuccess()
    {
        $cart_content = Cart::content();


        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();
        $banks = \App\Banks::all();

        return view('frontend/checkout-success')->with('page_title', 'Shipping Detail')
                                       ->with('users', $users)
                                       ->with('cart_content', $cart_content)
                                       ->with('banks', $banks)
                                        ->with('profiles', $profiles)
                                        ->with('latesnews', $latesnews)
                                        ->with('trf', $trf)
                                        ->with('categories', $categories)
                                        ->with('locations', $locations)
                                        ->with('client', $client);
    }


    public function getPayWithVeritrans()
    {


       // Veritrans::$serverKey = "VT-server-Bn5d6ZgXhx0r1ZfDjwH9d_8w"; // Demo di ganti dengan key
       Veritrans::$serverKey = "VT-server-Vg5bt91_UotU8q4bPe6LCOJa";
        // Uncomment for production environment
        Veritrans::$isProduction = true; // diganti jadi true

       // \Veritrans\Config::$serverKey = "VT-server-kU1z6e27mG6S5umt-G_q2fH0"; // di ganti dengan key production
        // Uncomment for production environment
       // \Veritrans\Config::$isProduction = false; // diganti jadi true


        $vt = new Veritrans;



     $transaction_details = array(
          'order_id' => rand(),
          'gross_amount' => Session::get('grand_total'), // no decimal allowed for creditcard

          );



     $ongkos_kirim = Session::get('weight_amount');





    // Optional

    $item_details = [];
    foreach (\Cart::content() as $item) {


    $item_details [] = [
            'id' => $item->id,
                'price' => $item->price,
                'quantity' => $item->qty,
                'weight' => $item->weight,
                'name' => $item->name,
                'discount' =>$item->discount,
                'subtotal' =>$item->subtotal,
        ];

    }
    // Optional
    $item_details [] = [
        'id' => 'a2',
        'price' => -Cart::totaldiskon(),
        'quantity' => 1,
        'name' => "Diskon"
     ];

     $item_details [] = [
            'id' => 0,
            'price' => $ongkos_kirim,
            'quantity' => 1,
            'name' => 'Ongkos Kirim'
        ];

        if(Session::has('coupons_id')){
                $kupon = Session::get('coupon');
                 $item_details [] = [
                    'id' => 'a3',
                    'price' => $kupon,
                    'quantity' => 1,
                    'name' => 'Kupon'
                ];
         }


        // Optional
        $billing_address = array(
            'first_name'    => Auth::user()->fullname,
            'last_name'     => " ",
            'address'       => Session::get('alamat'),
            'city'          => Session::get('kota'),
            'postal_code'   => Session::get('kodepos'),
            'phone'         => Session::get('telepon')

            );

        // Optional
        $shipping_address = array(
             'first_name'    => Auth::user()->fullname,
            'last_name'     => " ",
            'address'       => Session::get('alamat'),
            'city'          => Session::get('kota'),
            'postal_code'   => Session::get('kodepos'),
            'phone'         => Session::get('telepon')

            );

        // Optional
        $customer_details = array(
            'first_name'    => Auth::user()->fullname,
            'last_name'     => "",
            'email'         => Auth::user()->email,
            'phone'         => "",
            'billing_address'  => $billing_address,
            'shipping_address' => $shipping_address
            );

        // Fill transaction details
        $transaction_data = array(
            'payment_type' => 'vtweb',
            'vtweb' => array(
                'credit_card_3d_secure' => true,
            ),
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        );

        //return $transaction_data;

        try {
            $vtweb_url = $vt->vtweb_charge($transaction_data);
            return redirect($vtweb_url);
            // Redirect to Veritrans VTWeb page
            //header('Location: ' . Veritrans_VtWeb::getRedirectionUrl($params));
            //return redirect(\Veritrans\VtWeb::getRedirectionUrl($transaction));
        }
        catch (Exception $e) {
            $errors = $e->getMessage;
            return redirect('/shop/shipping-detail')->with('errors', $errors);

        }
    }

    public function getEmptyCart()
    {
        Cart::destroy();
        Session::forget('coupon');
        Session::forget('coupons_id');
        Session::forget('weight_amount');
        Session::forget('grand_total');
        return redirect('/shop/cart-content');
    }

    public function getDeleteCart($rowid)
    {
        Cart::remove($rowid);
        return redirect('/shop/cart-content');
    }



    public function getPaymentConfirmation($id){


        $cart_content = Cart::content();


        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();








        $invoice = Invoice::where('id', '=', $id)->get();
        $banks = \App\Banks::all();
        return view('frontend/payment-confirmation')->with('users', $users)
                                                   ->with('cart_content', $cart_content)
                                                   ->with('profiles', $profiles)
                                                    ->with('latesnews', $latesnews)
                                                    ->with('trf', $trf)
                                                    ->with('categories', $categories)
                                                    ->with('locations', $locations)
                                                    ->with('client', $client)
                                                    ->with('banks', $banks)
                                                    ->with('page_title', 'Konfirmasi Pembayaran')
                                                    ->with('invoice', $invoice);
    }
    public function postPaymentConfirmation(Request $request){

        $rules = array(
                    'invoice_id'    => 'required|exists:invoices,id',
                    'paid_amount'   => 'required|integer',
                    'acc_num'       => 'required',
                    'banks_id'          => 'required',
                    'payee'         => 'required'
            );

       $this->validate($request, $rules);

                    $tahun          = $request->get('tahun');
                    $bulan          = $request->get('bulan');
                    $tanggal        = $request->get('tanggal');

                     $invoice_id = $request->input('invoice_id');

                    $payment = new \App\Payment;
                    $payment->invoice_id    = $invoice_id;
                    $payment->paid_amount   = $request->input('paid_amount');
                    $payment->method        = 'transfer';
                    $payment->payee         = $request->input('payee');
                    $payment->banks_id      = $request->input('banks_id');
                    $branchs_id = \DB::table('invoice_details')->where('invoices_id', $invoice_id)->take(1)->value('branchs_id');

                    $payment->branchs_id = $branchs_id;
                    $payment->bank_receive  = $request->input('bank_receive');
                    $payment->trans_date    = $tahun.'-'.$bulan.'-'.$tanggal;
                    $payment->acc_num       = $request->input('acc_num');
                    $payment->save();

        $pay = DB::table('banks')
                                    ->join('payments', 'banks.id', '=', 'payments.banks_id')
                                    ->select('payments.*', 'banks.account_number', 'banks.name')
                                    ->where('payments.invoice_id', $invoice_id)
                                    ->get();

         //kirim email notif ke admin
        Mail::send('mail/payment-confirmation-admin',
                    ['pay' => $pay],
                    function ($m) use ($payment){
                    $m->to('apotemart.online@gmail.com', 'Admin Apotekmart')
                    ->subject('Pembayaran dari'.$payment->payee.'Untuk invoice No #'.$payment->invoice_id);
           });



        return redirect('/shop/confirmation-success');
    }
    public function getConfirmationSuccess(){


        $cart_content = Cart::content();


        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();


        return view('frontend/confirmation-success')->with('users', $users)
                                                   ->with('cart_content', $cart_content)
                                                   ->with('profiles', $profiles)
                                                    ->with('latesnews', $latesnews)
                                                    ->with('trf', $trf)
                                                    ->with('categories', $categories)
                                                    ->with('locations', $locations)
                                                    ->with('client', $client)

                                                    ->with('page_title', 'Konfirmasi Pembayaran');


    }
    public function getAdd($rowid){
        $item = \Cart::get($rowid);
        \Cart::update($rowid,
            [
            'qty' => $item->qty +1,
            'berat' => $item->berat +1
            ]);

        return redirect('/shop/cart-content');

    }
    public function getSubstract($rowid){
        $item = \Cart::get($rowid);
        \Cart::update($rowid,
            [
            'qty' => $item->qty - 1,
            'berat' => $item->berat -1
            ]);
        return redirect('/shop/cart-content');

    }


    //veritrans handler

    public function postNotif(Request $request)
    {

    }
    public function getSuccess()
    {
        // insert ke table invoice
        $today = new \DateTime(date('Y-m-d H:i:s'));
        $tomorrow = $today->modify('+1 day');

        $invoice = new Invoice;
        $invoice->order_id      = rand();

            $content = Cart::content();
            foreach($content as $con)
            {
                $branchlast = $con->branchs_id;
            }

        $invoice->branchs_id    = $branchlast;
        $invoice->users_id       = Auth::user()->id;
        $invoice->due_date      = $tomorrow;
        $invoice->sub_total     = Cart::total();
        $invoice->total_qty     = Cart::count();
        $invoice->weight_total  = Cart::totalberat();
        $invoice->weight_amount = Session::get('weight_amount');
        $invoice->total_discount = Cart::totaldiskon();
         if(Session::has('coupons_id')){
            $invoice->coupons_id = Session::pull('coupons_id');
            $invoice->coupon_disc = Session::pull('coupon');
        }
        $invoice->total_amount  = Session::get('grand_total');
        if(Session::has('notice')) {
            $invoice->notice  = Session::pull('notice');
        }
        $invoice->status        = 'veritrans';
        $invoice->save();

        if($invoice->coupons_id > 0){
            $id_coupon = $invoice->coupons_id;
            $coupon_counter = \App\Coupons::where('id', $id_coupon)->value('counter');

            $counter = $coupon_counter + 1;

            $coupon = DB::table('coupons')
                            ->where('id', $id_coupon)
                            ->update(['counter' => $counter]);


        }

         //insert ke invoice datail  setat invoice di save pagil invoice id untuk detail = $invoice->id;

         $payment = new \App\Payment;
        $payment->invoice_id    = $invoice->id;
        $payment->paid_amount   = Session::get('grand_total');
        $payment->trans_date    = $today;
        $payment->method       = 'veritrans';
        $payment->payee         = Auth::user()->fullname;
        $payment->banks_id          = '';
        $payment->branchs_id    = '';
        $payment->acc_num       = '';
        $payment->status        = 'checked';
        $payment->save();

        $cart_content = Cart::content();

        foreach ($cart_content as $item) {
            $invoice_detail = new InvoiceDetail;
            $invoice_detail->invoices_id        =  $invoice->id;
            $invoice_detail->product_branchs_id =  $item->id;
            $invoice_detail->branchs_id         =  $item->branchs_id;

            $invoice_detail->qty                =  $item->qty;
            $invoice_detail->weight              =  $item->weight;

            //if($item->sales_price == 0)
            //{
              $invoice_detail->price              =  $item->price;
            //}else{
              // $invoice_detail->price              =  $item->sales_price;
            //}

            $disc1 = $item->price - $item->sales_price;
            $disc2 = $disc1 == 0 ? 0 : ($disc1 / $item->price)*100;

            $disc3 = $disc1 == $item->price ? 0 : ($disc1 / $item->price)*100 ;


            $invoice_detail->discount             =  $disc3;
            $invoice_detail->subtotal           =  $item->subtotal;
            $invoice_detail->save();


            //stock ajustemt start

            $start_stock = \App\ProductBranchs::where('id', '=', $item->id)->value('stock');

            $end_stock = $start_stock - $item->qty;

            $product_id = \App\ProductBranchs::where('id', '=', $item->id)->value('products_id');


            $productadjustment                            = new \App\ProductAdjustments;
            $productadjustment->adjustment_types_id       = 2;
            $productadjustment->branchs_id                = $item->branchs_id;
            $productadjustment->products_id               = $product_id;
            $productadjustment->start_stock               = $start_stock;
            $productadjustment->qty                       = -$item->qty;
            $productadjustment->end_stock                 = $end_stock;
            $productadjustment->price                     = $item->price;
            $productadjustment->sales_price               = $item->sales_price;
            $productadjustment->status                    = 'publish';
            $productadjustment->users_id                  = \Auth::user()->id;
            $productadjustment->save();

            \DB::table('product_branchs')
                        ->where('branchs_id', $item->branchs_id)
                        ->where('id', $item->id)
                        ->update(['stock' => $end_stock]);


            //end stock adjustment


        }


        $shipping_invoice = new \App\ShippingInvoices;
        $shipping_invoice->order_id = $invoice->order_id;
        $shipping_invoice->shipping_amount = $invoice->weight_amount;
        $shipping_invoice->save();


        $banks = \App\Banks::all();

        $tgl1 = $invoice->due_date;
        $due_date = $tgl1->format('d M Y H:i:s');

        $hariini = new \DateTime(date('Y-m-d H:i:s'));
        $tgl = $hariini->format('d M Y H:i:s');

        //kirim email notif ke customer
        $user = Auth::user();
        Mail::send('mail/checkout-customer-veritrans',
                    ['invoice' => $invoice, 'banks' => $banks, 'due_date' => $due_date, 'tgl' => $tgl],
                    function ($m) use ($user, $invoice) {
                    $m->to($user->email, $user->fullname)
                    ->subject('Invoice #'.$invoice->id);

           });



        //kirim email notif ke admin
        Mail::send('mail/checkout-admin-veritrans',
                    ['invoice' => $invoice, 'user' => $user, 'due_date' => $due_date, 'tgl' => $tgl],
                    function ($m) {
                    $m->to('apotekmart.online@gmail.com', 'Admin -Apotekmart')
                    ->subject('New Order');
           });


        Cart::destroy();
       Session::forget('coupon');
       Session::forget('coupons_id');

        return redirect('/shop/veritrans-payment-success');
    }


     public function postSearch(Request $request){

        $keyword = $request->input('keyword');

        $cart_content = \Cart::content();

        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = User::where('group_id', 2)->count();
        $categories = \App\ProductCategories::orderby('name')->get();


        $bests = DB::table('ratings')
                            ->select('product_branchs_id',
                                    DB::raw('avg(rate) as rate'))
                            ->groupby('product_branchs_id')
                            ->orderby('rate', 'Desc')
                            ->take(3)
                            ->get();
        $brands = \App\Brands::orderby('name')->get();


        $products = DB::table('products')
                             ->join('product_branchs', 'product_branchs.products_id', '=', 'products.id')
                             ->select('products.*', 'product_branchs.status')
                             ->where('product_branchs.status', 'Publish')
                             ->where('products.name', 'Like', $keyword.'%')
                             ->orwhere('products.meta_keywords', 'like', '%'.$keyword.'%')
                             ->orderby('products.name')
                             ->get();


        $count = DB::table('products')
                             ->join('product_branchs', 'product_branchs.products_id', '=', 'products.id')
                             ->select('products.*', 'product_branchs.status')
                             ->where('product_branchs.status', 'Publish')
                             ->where('products.name', 'Like', $keyword.'%')
                             ->orwhere('products.meta_keywords', 'like', '%'.$keyword.'%')
                             ->orderby('products.name')
                             ->count();

        //$products = DB::table('product_branchs')->where('name', 'Like', $keyword.'%')
          //                                      ->where('status', '=', 'Publish')
            //                                    ->paginate(9);
        //$count = DB::table('product_branchs')->where('name', 'Like', $keyword.'%')
          //                            ->where('status', 'Publish')
            //                         ->count();

        return view('frontend/search')
                                    ->with('products', $products)
                                   ->with('cart_content', $cart_content)
                                   ->with('page_title', 'Search')
                                    ->with('profiles', $profiles)
                                    ->with('latesnews', $latesnews)
                                    ->with('trf', $trf)
                                    ->with('brands', $brands)
                                    ->with('categories', $categories)
                                    ->with('bests', $bests)
                                    ->with('count', $count)
                                    ->with('client', $client);
    }

    public function getCancel()
    {
        return redirect('/shop/checkout');
    }
    public function getError()
    {
        return redirect('/shop/checkout');
    }
    public function getVeritransPaymentSuccess()
    {

        $cart_content = Cart::content();


        $profiles = \App\Profiles::all();
        $latesnews = \App\LatesNews::orderby('created_at', 'Desc')->take(3)->get();

        $trf = \App\Invoice::where('status', '<>', 'unpaid')->where('status', '<>', 'canceled')->count();
        $client = \App\User::where('group_id', 2)->count();
         $categories = \App\ProductCategories::orderby('name')->get();
        $users = User::where('id', '=', Auth::user()->id)->get();
        $locations = \App\ShippingRates::orderby('destination')->get();


        return view('frontend/payment-success')->with('page_title', 'Payment Success')
                                                ->with('users', $users)
                                               ->with('cart_content', $cart_content)
                                               ->with('profiles', $profiles)
                                                ->with('latesnews', $latesnews)
                                                ->with('trf', $trf)
                                                ->with('categories', $categories)
                                                ->with('locations', $locations)
                                                ->with('client', $client);
    }
    public function getFinishPayment()
    {
         // insert ke table invoice
        $today = new \DateTime(date('Y-m-d H:i:s'));
        $tomorrow = $today->modify('+1 day');

        $invoice = new Invoice;
        $invoice->order_id      = rand();

            $content = Cart::content();
            foreach($content as $con)
            {
                $branchlast = $con->branchs_id;
            }

        $invoice->branchs_id    = $branchlast;
        $invoice->users_id       = Auth::user()->id;
        $invoice->due_date      = $tomorrow;
        $invoice->sub_total     = Cart::total();
        $invoice->total_qty     = Cart::count();
        $invoice->weight_total  = Cart::totalberat();
        $invoice->weight_amount = Session::get('weight_amount');
        $invoice->total_discount = Cart::totaldiskon();
         if(Session::has('coupons_id')){
            $invoice->coupons_id = Session::pull('coupons_id');
            $invoice->coupon_disc = Session::pull('coupon');
        }
        $invoice->total_amount  = Session::get('grand_total');
        if(Session::has('notice')) {
            $invoice->notice  = Session::pull('notice');
        }
        $invoice->status        = 'veritrans';
        $invoice->save();

        if($invoice->coupons_id > 0){
            $id_coupon = $invoice->coupons_id;
            $coupon_counter = \App\Coupons::where('id', $id_coupon)->value('counter');

            $counter = $coupon_counter + 1;

            $coupon = DB::table('coupons')
                            ->where('id', $id_coupon)
                            ->update(['counter' => $counter]);


        }

         //insert ke invoice datail  setat invoice di save pagil invoice id untuk detail = $invoice->id;

         $payment = new \App\Payment;
        $payment->invoice_id    = $invoice->id;
        $payment->paid_amount   = Session::get('grand_total');
        $payment->trans_date    = $today;
        $payment->method       = 'veritrans';
        $payment->payee         = Auth::user()->fullname;
        $payment->banks_id      = '';
        $payment->branchs_id    = '';
        $payment->acc_num       = '';
        $payment->status        = 'checked';
        $payment->save();

        $cart_content = Cart::content();

        foreach ($cart_content as $item) {
            $invoice_detail = new InvoiceDetail;
            $invoice_detail->invoices_id        =  $invoice->id;
            $invoice_detail->product_branchs_id =  $item->id;
            $invoice_detail->branchs_id         =  $item->branchs_id;

            $invoice_detail->qty                =  $item->qty;
            $invoice_detail->weight              =  $item->weight;

            //if($item->sales_price == 0)
            //{
              $invoice_detail->price              =  $item->price;
            //}else{
              // $invoice_detail->price              =  $item->sales_price;
            //}

            $disc1 = $item->price - $item->sales_price;
            $disc2 = $disc1 == 0 ? 0 : ($disc1 / $item->price)*100;

            $disc3 = $disc1 == $item->price ? 0 : ($disc1 / $item->price)*100 ;


            $invoice_detail->discount             =  $disc3;
            $invoice_detail->subtotal           =  $item->subtotal;
            $invoice_detail->save();


            //stock ajustemt start

            $start_stock = \App\ProductBranchs::where('id', '=', $item->id)->value('stock');

            $end_stock = $start_stock - $item->qty;

            $product_id = \App\ProductBranchs::where('id', '=', $item->id)->value('products_id');


            $productadjustment                            = new \App\ProductAdjustments;
            $productadjustment->adjustment_types_id       = 2;
            $productadjustment->branchs_id                = $item->branchs_id;
            $productadjustment->products_id               = $product_id;
            $productadjustment->start_stock               = $start_stock;
            $productadjustment->qty                       = -$item->qty;
            $productadjustment->end_stock                 = $end_stock;
            $productadjustment->price                     = $item->price;
            $productadjustment->sales_price               = $item->sales_price;
            $productadjustment->status                    = 'publish';
            $productadjustment->users_id                  = \Auth::user()->id;
            $productadjustment->save();

            \DB::table('product_branchs')
                        ->where('branchs_id', $item->branchs_id)
                        ->where('id', $item->id)
                        ->update(['stock' => $end_stock]);


            //end stock adjustment


        }


        $shipping_invoice = new \App\ShippingInvoices;
        $shipping_invoice->order_id = $invoice->order_id;
        $shipping_invoice->shipping_amount = $invoice->weight_amount;
        $shipping_invoice->save();


        $banks = \App\Banks::all();

        $tgl1 = $invoice->due_date;
        $due_date = $tgl1->format('d M Y H:i:s');

        $hariini = new \DateTime(date('Y-m-d H:i:s'));
        $tgl = $hariini->format('d M Y H:i:s');

        //kirim email notif ke customer
        $user = Auth::user();
        Mail::send('mail/checkout-customer-veritrans',
                    ['invoice' => $invoice, 'banks' => $banks, 'due_date' => $due_date, 'tgl' => $tgl],
                    function ($m) use ($user, $invoice) {
                    $m->to($user->email, $user->fullname)
                    ->subject('Invoice #'.$invoice->id);

           });



        //kirim email notif ke admin
        Mail::send('mail/checkout-admin-veritrans',
                    ['invoice' => $invoice, 'user' => $user, 'due_date' => $due_date, 'tgl' => $tgl],
                    function ($m) {
                    $m->to('apotekmart.online@gmail.com', 'Admin - Apotekmart')
                    ->subject('New Order');
           });


        Cart::destroy();
       Session::forget('coupon');
       Session::forget('coupons_id');

        return redirect('/shop/veritrans-payment-success');
    }



     public function getUnfinishPayment(){

        return redirect('/shop/cart-content')
               ->with(
                   'message',
                   'Anda membatalkan pembayaran, silahkan ulangi lagi proses pembayaran'
               );

     }
    //end veritrans



}
