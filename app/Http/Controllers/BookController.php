<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\ContentTypeChecker;
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
            $data = $book->getBookLinks();
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

        $contentChecker = new ContentTypeChecker();
        if($contentChecker->isNotApplicationJsonContentType($request)){
            $response = $contentChecker->setWrongContentTypeResponse();
            return $response;
        }
        $data_json = $request->instance()->getContent();
        $data = json_decode($data_json);
        $author_id = $data->author_id;
        $title = $data->title;
        $copies_available = $data->copies_available;
        $autor = Author::where('id','=',$author_id)->get();
        if(count($autor)==0 || empty($title) || empty($copies_available)){
            return response(json_encode(['message'=>' Bad request - Not all data is provided or author not peviously added'],JSON_UNESCAPED_SLASHES),400);
        }else{
            $book =  Book::create(
                [
                    'title'=> $title,
                    'author_id'=>$author_id,
                    'copies_available' => $copies_available,
                ]
            );
        }
        return response(json_encode($book),200);
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
     * Update the specified resource in storage.a
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $contentChecker = new ContentTypeChecker();
        if($contentChecker->isNotApplicationJsonContentType($request)){
            $response = $contentChecker->setWrongContentTypeResponse();
            return $response;
        }
        $data_json = $request->instance()->getContent();
        $data = json_decode($data_json);
        $author_id = $data->author_id;
        $title = $data->title;
        $copies_available = $data->copies_available;
        $book = Book::where('id','=',$id)->get();
        if(count($book)==0){
            return response(json_encode(['message'=>'Book does not exist'],JSON_UNESCAPED_SLASHES),404);
        }else{
            $book = $book->first();
            $book->author_id =  $author_id;
            $book->title = $title;
            $book->copies_available = $copies_available;
            $book->save();
            return response(json_encode($book),200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::where('id','=',$id)->get();
        if(count($book)==0){
            return response(json_encode(['message'=>'Book does not exist'],JSON_UNESCAPED_SLASHES),404);
        }else{
            $book->first()->delete();
           return response(json_encode(['message'=>'Record '.$id.' succsessfuly deleted.'],JSON_UNESCAPED_SLASHES),200);
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $term
     * @return \Illuminate\Http\Response
     */
    public function find($term)
    {   $books = Book::where('title','like','%'.$term.'%')->get();
        if(count($books)!==0){
            $data = new stdClass();
            $data->books = [];
            $data->_links = ['_self' =>'http://library-assignment.filipivanko.com/api/books/find/'.$term];
            foreach ($books as $book) {
                array_push($data->books, $book->getBookLinks());
            }
            return  response(json_encode($data,JSON_UNESCAPED_SLASHES),200);
        }else{
            return  response(json_encode(['message'=>'No records found matching the search term'],JSON_UNESCAPED_SLASHES),404);
        }
    }
}
