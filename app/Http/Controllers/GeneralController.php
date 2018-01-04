<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getAbout()
    {
      $about = \App\About::all();
      return $about->toJson();
    }

    public function getOfficialPartner()
    {
      $official = \App\OfficialPartner::all();
      return $official->toJson();

    }

    public function getOurActivity()
    {
      $activity = \App\SellerStory::all();
      return $activity->toJson();
    }

    public function getHowToShop()
    {
      $shop = \App\HowToshop::all();
      return $shop->toJson();
    }

    public function getPayment()
    {
      $payment = \App\Payment::all();
      return $payment->toJson();
    }

    public function getRefund()
    {
      $refund = \App\Refund::all();
      return $refund->toJson();
    }

    public function getHowToSell()
    {
      $sell = \App\HowTosell::all();
      return $sell->toJson();
    }

    public function getWhitdrawal()
    {
      $withdrawal = \App\Withdrawal::all();
      return $withdrawal->toJson();
    }

    public function getContact()
    {
      $contact = \App\Contact::all();
      return $contact->toJson();
    }
}
