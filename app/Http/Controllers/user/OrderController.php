<?php

namespace App\Http\Controllers\User;

use App\Models\Order;
use App\Models\Promo;
use App\Models\RiwayatPesanan;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Exception;

class OrderController extends Controller
{
    public function uploadProof(Request $request, Order $order)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $path = $request->file('bukti_pembayaran')->store('payment_proof', 'public');

            $order->update([
                'bukti_pembayaran' => $path
            ]);
        }

        return redirect()->back()->with('success', 'Payment proof uploaded successfully!');
    }

    public function showReceipt(Order $order)
    {
        if (auth()->id() !== $order->id_user) {
            abort(403, 'Unauthorized action.');
        }

        return view('user.receipt', compact('order'));
    }

    public function applyPromo(Request $request)
    {
        $validatedData = $request->validate([
            'promo_code' => 'required|string',
            'order_details' => 'required|array',
            'order_details.fabric_type' => ['required', Rule::in(['kain katun', 'kain mori', 'kain sutera'])],
            'order_details.jumlah' => 'required|integer|min:1',
            'order_details.payment_method' => ['required', Rule::in(['bank_transfer', 'qris'])],
        ]);

        $promo = Promo::where('code', $validatedData['promo_code'])->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Invalid promo code.'], 404);
        }

        if ($promo->expires_at && $promo->expires_at < now()) {
            return response()->json(['success' => false, 'message' => 'This promo code has expired.'], 400);
        }

        if ($promo->max_uses) {
            if ($promo->max_uses_scope === 'global' && $promo->current_uses >= $promo->max_uses) {
                return response()->json(['success' => false, 'message' => 'This promo code has reached its usage limit.'], 400);
            }
            if ($promo->max_uses_scope === 'personal') {
                $userUsageCount = Order::where('id_user', Auth::id())->where('promo_code', $promo->code)->count();
                if ($userUsageCount >= $promo->max_uses) {
                    return response()->json(['success' => false, 'message' => 'You have already used this promo code the maximum number of times.'], 400);
                }
            }
        }

        if ($promo->constraints) {
            foreach ($promo->constraints as $constraint) {
                $type = $constraint['type'];
                $value = $constraint['value'];
                $orderValue = $request->input('order_details.' . $type);

                if ($orderValue !== $value) {
                    $friendlyType = str_replace('_', ' ', $type);
                    return response()->json([
                        'success' => false,
                        'message' => "This promo is not valid for the selected {$friendlyType}."
                    ], 400);
                }
            }
        }

        $basePrice = 300000;
        $fabricCost = 0;
        if ($validatedData['order_details']['fabric_type'] === 'kain mori') {
            $fabricCost = 100000;
        } elseif ($validatedData['order_details']['fabric_type'] === 'kain sutera') {
            $fabricCost = 300000;
        }

        $totalBeforeDiscount = ($basePrice + $fabricCost) * $validatedData['order_details']['jumlah'];
        
        $discountAmount = 0;
        if ($promo->type === 'fixed') {
            $discountAmount = $promo->value;
        } elseif ($promo->type === 'percentage') {
            $discountAmount = ($totalBeforeDiscount * $promo->value) / 100;
        }
        
        $discountAmount = min($totalBeforeDiscount, $discountAmount);

        Session::put('promo', [
            'code' => $promo->code,
            'type' => $promo->type,
            'value' => $promo->value,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Promo code applied successfully!',
            'promo' => Session::get('promo'),
            'discount_amount' => $discountAmount
        ]);
    }

    public function removePromo(Request $request)
    {
        $request->session()->forget('promo');
        return back()->with('success', 'Promo code removed.');
    }
    
    public function store(Request $request)
    {
        Log::info('OrderController@store called with request data: ', $request->all());
        $validatedData = $request->validate([
        'size' => 'required|in:XS,S,M,L,XL,custom',
        'design_file' => 'required|image|mimes:png,jpeg,jpg|max:2048',
        'garment_type' => 'required|in:shirt,dress',

        'custom_body_length' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'shirt'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_sleeve_length' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'shirt'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_shoulder_width' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'shirt'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_body_width' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'shirt'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_neck_size' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'shirt'),
            'nullable', 'numeric', 'min:1'
        ],

        'custom_dress_body_length' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'dress'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_dress_sleeve_length' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'dress'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_dress_shoulder_width' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'dress'),
            'nullable', 'numeric', 'min:1'
        ],
        'custom_dress_body_width' => [
            Rule::requiredIf(fn() => $request->input('size') === 'custom' && $request->input('garment_type') === 'dress'),
            'nullable', 'numeric', 'min:1'
        ],
    ]);

        $sizeDetails = '';

        if ($validatedData['size'] === 'custom') {
            $sizeDetails = 'custom: ';
            $measurements = [];

            if ($validatedData['garment_type'] === 'shirt') {
                $measurements = [
                    'bl:' . $validatedData['custom_body_length'],
                    'sl:' . $validatedData['custom_sleeve_length'],
                    'sw:' . $validatedData['custom_shoulder_width'],
                    'bw:' . $validatedData['custom_body_width'],
                    'ns:' . $validatedData['custom_neck_size'],
                ];
            } else {
                $measurements = [
                    'bl:' . $validatedData['custom_dress_body_length'],
                    'sl:' . $validatedData['custom_dress_sleeve_length'],
                    'sw:' . $validatedData['custom_dress_shoulder_width'],
                    'bw:' . $validatedData['custom_dress_body_width'],
                ];
            }
            $sizeDetails .= implode(', ', $measurements);
        } else {
            $sizeDetails = $validatedData['size'];
        }

        $designPath = $request->file('design_file')->store('designs', 'public');

        $request->session()->put('order_details', [
            'ukuran' => $sizeDetails,
            'desain' => $designPath,
        ]);

        return redirect()->route('checkout.show');
    }
    
    public function showCheckout(Request $request)
    {
        if (!$request->session()->has('order_details')) {
            return redirect()->route('ukuran')->with('error', 'Please select a size and design first.');
        }

        $orderDetails = $request->session()->get('order_details');
        $imageUrl = asset('storage/' . $orderDetails['desain']);
        $basePrice = 300000;
        $promoDetails = $this->getPromoDetails($basePrice);

        return view('user.checkout', [
            'uploadedBatikUrl' => $imageUrl,
            'basePrice'        => $basePrice,
            'discountAmount'   => $promoDetails['discountAmount'],
            'finalPrice'       => $promoDetails['finalPrice'],
        ]);
    }

    public function storeOrder(Request $request)
    {
        $orderDetails = $request->session()->get('order_details');
        if (!$orderDetails) {
            return redirect()->route('ukuran')->with('error', 'Your session has expired. Please start again.');
        }

        $validatedCheckoutData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'street_address' => 'required|string|max:500',
            'phone' => 'required|string|min:10|max:15',
            'payment_method' => 'required|in:bank_transfer,qris',
            'additional_note' => 'nullable|string|max:1000',
            'fabric_type' => 'required|string|in:kain katun,kain mori,kain sutera',
            'jumlah' => 'required|integer|min:1',
        ]);

        $basePrice = 300000;
        $fabricCost = 0;
        if ($validatedCheckoutData['fabric_type'] === 'kain mori') {
            $fabricCost = 100000;
        } elseif ($validatedCheckoutData['fabric_type'] === 'kain sutera') {
            $fabricCost = 300000;
        }

        $totalBeforeDiscount = ($basePrice + $fabricCost) * $validatedCheckoutData['jumlah'];
        $promoDetails = $this->getPromoDetails($totalBeforeDiscount);
        
        $user = Auth::user();
        $order = null;

        try {
            DB::transaction(function () use ($validatedCheckoutData, $orderDetails, $user, &$order, $promoDetails) {
                $order = Order::create([
                    'id_user'         => $user->id,
                    'email'           => $user->email,
                    'nama'            => $validatedCheckoutData['first_name'] . ' ' . $validatedCheckoutData['last_name'],
                    'alamat'          => $validatedCheckoutData['street_address'],
                    'no_telepon'      => $validatedCheckoutData['phone'],
                    'ukuran'          => $orderDetails['ukuran'],
                    'fabric_type'      => $validatedCheckoutData['fabric_type'],
                    'desain'          => $orderDetails['desain'],
                    'metode_bayar'    => $validatedCheckoutData['payment_method'],
                    'tanggal_pesan'   => now(),
                    'tanggal_estimasi' => null,
                    'status'          => 'Pending',
                    'nota'            => $validatedCheckoutData['additional_note'],
                    'jumlah'          => $validatedCheckoutData['jumlah'],
                    'total'           => $promoDetails['finalPrice'],
                    'promo_code'      => $promoDetails['promoCodeUsed'],
                    'discount_amount' => $promoDetails['discountAmount'],
                ]);

                RiwayatPesanan::create([
                    'user_id'  => $user->id,
                    'order_id' => $order->id_pesanan, 
                ]);
                
                Notification::create([
                    'user_id'  => $user->id,
                    'order_id' => $order->id_pesanan,
                    'title'    => 'Your order has been placed',
                    'message'  => 'Please wait while we confirm your order within 24 hours.',
                ]);
                
                if ($promoDetails['promoCodeUsed']) {
                    $promo = Promo::where('code', $promoDetails['promoCodeUsed'])->first();
                    if ($promo) {
                        $promo->increment('current_uses');
                    }
                }
            });
        } catch (Exception $e) {
            return back()->with('error', 'An unexpected error occurred while placing your order. Please try again.')->withInput();
        }

        $request->session()->forget(['order_details', 'promo']);

        return redirect()->route('receipt', ['order' => $order->id_pesanan])
                         ->with('success', 'Your order has been placed successfully!');
    }
    
    private function getPromoDetails(float $basePrice): array
    {
        $finalPrice = $basePrice;
        $discountAmount = 0;
        $promoCodeUsed = null;

        if (Session::has('promo')) {
            $promoData = Session::get('promo');
            
            if ($promoData['type'] === 'fixed') {
                $discountAmount = $promoData['value'];
            } elseif ($promoData['type'] === 'percentage') {
                $discountAmount = ($basePrice * $promoData['value']) / 100;
            }
            
            $finalPrice = max(0, $basePrice - $discountAmount);
            $promoCodeUsed = $promoData['code'];
        }

        return [
            'finalPrice'      => $finalPrice,
            'discountAmount'  => $discountAmount,
            'promoCodeUsed'   => $promoCodeUsed,
        ];
    }
}