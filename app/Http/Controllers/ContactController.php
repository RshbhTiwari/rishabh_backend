<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contact;
use App\Models\User;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function saveContactInfo(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        try {
            $user = User::where('email', $request->email)->first();

            // Save the contact form data
            Contact::create([
                'user_id' => $user->id ?? null,
                'name' => $request->name,
                'email' => $request->email,
                'message' => $request->message,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Contact form submitted successfully!'
            ], 201);
        } catch (\Exception $e) {
            // Return an error response
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong while submitting the form. Please try again later.'
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'product_id'   => 'required|exists:products,id',
            'username'    => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'rating'       => 'required|integer|min:1|max:5',
            'comment'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create the review
        $review = new Review();
        $review->product_id = $request->product_id;
        $review->firstname = $request->username;
        $review->email = $request->email;
        $review->rating = $request->rating;
        $review->comment = $request->comment;

        // If user is logged in, save user_id
        if (Auth::check()) {
            $review->user_id = Auth::id();
        }

        $review->save();

        return response()->json(['message' => 'Review submitted successfully'], 201);
    }


    // Previous store method

    /**
     * Get the latest reviews for a specific product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductReviews($productId)
    {
        // Get latest reviews for the specified product
        $reviews = Review::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }
}
