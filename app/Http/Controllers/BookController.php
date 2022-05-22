<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use stdClass;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Book::all();
        $responseData = new stdClass();
        $responseData->_links = ['_self' =>'http://library-assignment.filipivanko.com/api/books'];
        $responseData->books = [];
        foreach ($books as $book){
            $data = $book->getBookInfo('book_profile');
            array_push($responseData->books, $data);
        }

        return response(json_encode($responseData,JSON_UNESCAPED_SLASHES),200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::all()->where('id','=',$id)->first();
        if($book !== null){
            $data = $book->getBookInfo('_self');
            return response(json_encode($data,JSON_UNESCAPED_SLASHES),200);
        }else{
            return  response(json_encode(['message'=>'ID not found'],JSON_UNESCAPED_SLASHES),404);
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

}
