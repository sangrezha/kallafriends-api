<?php

namespace App\Http\Controllers;

use App\MemberAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{

  public function getPoint(Request $request)
  {
    $data = MemberAsset::select(DB::raw('current_value+credit-debet as point'))
      ->orderBy('post_date', 'DESC')
      ->firstWhere('id_member', $request->user()->id);

    $responseArr['data'] = (int) optional($data)->point;
    $responseArr['status'] = 200;
    $responseArr['messages'] = ["success" => "SUCCESS"];
    return response()->json($responseArr);
  }
}
