<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\LoginCode;
use App\Models\Customer;
use Carbon\Carbon;
use Firebase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use nusoap_client;
use Push;


class ApiController extends Controller
{


    public function checkEmail(Request $request)
    {
        $data['exist_customer'] = false;
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reg_id' => 'required|min:10',
        ]);
        if ($validator->fails()) {
            $data['error'] = $validator->errors()->all();
            $data['status'] = false;
        } else {
            $customer_exist = Customer::where('email', $request->email)->first();
            if ($customer_exist) {
                $customer = Customer::find($customer_exist->id);
                $customer->update(['network' => 'external', 'country' => $request->country, 'reg_id' => $request->reg_id]);
                $data['exist_customer'] = true;
            } else {
                $customer = new Customer;
                $customer->email = $request->email;
                $customer->country = $request->country;
                $customer->network = 'external';
                $customer->reg_id = $request->reg_id;
                $customer->save();
                $data['exist_customer'] = false;
            }
            if (!$customer) {
                $data['status'] = false;
                $data['error'][0] = "کد خطا : 1";
            } else {
                $login_code = $this->_make_login_code($customer->id);
                if ($login_code['status'] == true) {
                    $data['status'] = true;
                } else {
                    $data['error'][0] = "کد خطا : 2";
                }
            }
        }
        return $data;
    }

    public function checkMobile(Request $request)
    {

        $sms_client = new nusoap_client('http://api.payamak-panel.com/post/send.asmx?wsdl');


        $parameters['username'] = "09309268176";
        $parameters['password'] = "1234";

        $parameters['from'] = "2122480075";
        $parameters['to'] = $request->mobile;
        $parameters['isflash'] = false;


        $data['exist_customer'] = false;
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|mobile',
            'reg_id' => 'required|min:10',
        ]);
        if ($validator->fails()) {
            $data['error'] = $validator->errors()->all();
            $data['status'] = false;
        } else {
            $customer_exist = Customer::where('mobile', $request->mobile)->first();
            if ($customer_exist) {
                $customer = Customer::find($customer_exist->id);
                $customer->update(['network' => 'internal', 'reg_id' => $request->reg_id]);
                $data['exist_customer'] = true;

            } else {
                $customer = new Customer;
                $customer->mobile = $request->mobile;
                $customer->network = 'internal';
                $customer->reg_id = $request->reg_id;
                $customer->save();
                $data['exist_customer'] = false;


            }


            if (!$customer) {
                $data['status'] = false;
                $data['error'][0] = "کد خطا : 1";

            } else {

                $login_code = $this->_make_login_code($customer->id);
                $parameters['text'] = $login_code;
                $data['sms'] = $sms_client->call('SendSimpleSMS2', $parameters);
                if ($login_code['status'] == true) {
                    $data['status'] = true;
                } else {
                    $data['error'][0] = "کد خطا : 2";
                }

            }
        }
        return $data;

    }

    public function checkLoginCode(Request $request)
    {


        $firebase = new Firebase();
        $push = new Push();
        $payload = array();
        $payload['log_out'] = '1';


        $push->setTitle("");
        $push->setMessage("");
        $push->setImage('');
        $push->setIsBackground(TRUE);
        $push->setPayload($payload);
        $json = '';
        $response = '';


        $data['status'] = false;

        $validator = Validator::make($request->all(), [
            'code' => 'required|min:4|numeric',
            'mobile' => 'mobile',
            'email' => 'email',
            'reg_id' => 'required|min:10',
        ]);
        if ($validator->fails()) {
            $data['error'] = $validator->errors()->all();
            $data['status'] = false;
        } else {

            if ($request->mobile != "") {
                $login_code = $this->_get_customer($request->code, ($request->mobile != "") ? $request->mobile : $request->email, "mobile");
            } else if ($request->email != "") {
                $login_code = $this->_get_customer($request->code, ($request->mobile != "") ? $request->mobile : $request->email, "email");
            } else {
                $login_code = null;
                $data['status'] = false;
                $data['error'][0] = "کد خطا : 3";
            }
            if ($login_code['status'] == true) {

                $customer = $login_code['customer'];
                $json = $push->getPush();
                $regId = $customer->reg_id;
                $data['customer_regId'] = $customer->reg_id;
                $data['push'] = $firebase->send($regId, $json);
                if ($customer->reg_id != $request->reg_id) {
                    if ($customer->device_count < 5) {
                        $customer->device_count = $customer->device_count + 1;
                        $customer->reg_id = $request->reg_id;
                        $customer->save();
                        $login_code['login_code']->delete();
                        $data['status'] = true;
                        $data['customerId'] = $customer->id;
                    } else {
                        $data['status'] = false;
                        $data['error'][0] = "شما بادستگاه های مختلف ، با این حساب بیش از حد مجاز وارد شدید";
                    }
                } else {
                    $data['status'] = true;
                    $data['customerId'] = $customer->id;
                }


            } else {
                $data['status'] = false;
                $data['error'][0] = "کد وارد شده معتبر نیست";
            }


        }
        return $data;
    }

    public function customerLoginOrRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'min:5|max:255',
            'mobile' => 'mobile',
            'email' => 'email',
            'image' => '',
        ]);
        if ($validator->fails()) {
            $data['error'] = $validator->errors()->all();
            $data['status'] = false;
        } else {

            $customer = $login_code['customer'];
            if ($request->name != "") {
                $customer->name = $request->name;
            }

            if ($request->mobile != "") {
                $customer->network = "internal";
            } else {
                $customer->network = "external";
            }
            if ($request->image != "") {
                $customer->image = $request->image;
            }

            $customer->save();
            if (!$customer) {
                $data['status'] = false;
                $data['error'][0] = "کد خطا : 1";
            } else {
                $data['status'] = true;
            }


        }
        return $data;

    }

    private function _make_login_code($customer_id)
    {
        $customer = Customer::find($customer_id);
        $loginCode = new LoginCode;
        $loginCode->code = random_int(1000, 99999);
        $loginCode->expire = Carbon::now()->addMinutes(3);
        if (!$customer) {
            $data['status'] = false;
        } else {
            $customer->loginCode()->delete();
            $customer->loginCode()->save($loginCode);
            $data['status'] = true;
        }
        return $data;
    }

    private function _get_customer($code, $unique_value, $type)
    {
        $data['status'] = false;
        $login_code = LoginCode::where('code', $code)->where('expire', '>', Carbon::now())->first();
        if ($login_code) {
            if ($login_code->customer()->where($type, $unique_value)->first()) {
                $data['status'] = true;
                $data['customer'] = $login_code->customer;
                $data['login_code'] = $login_code;
            } else {
                $data['status'] = false;
            }

        }
        return $data;
    }
}
