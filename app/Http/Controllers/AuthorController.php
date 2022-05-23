<?php

namespace App\Http\Controllers;

use App\ContentTypeChecker;
use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;
use stdClass;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $authors = Author::all();
        $responseData = new stdClass();
        $responseData->_links = ['_self' =>'http://library-assignment.filipivanko.com/api/authors'];
        $responseData->authors = [];
        foreach ($authors as $author){
            $data = $author->getAuthorInfo('author_profile');
            array_push($responseData->authors, $data);
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
        $name = $data->name;
            $author =  Author::create(
                [
                    'name'=> $name,
                ]
            );
        return response(json_encode($author),200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $author = Author::all()->where('id','=',$id)->first();
        if($author !== null){
            $data = $author->getAuthorInfo('_self');
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
        $contentChecker = new ContentTypeChecker();
        if($contentChecker->isNotApplicationJsonContentType($request)){
            $response = $contentChecker->setWrongContentTypeResponse();
            return $response;
        }
        $data_json = $request->instance()->getContent();
        $data = json_decode($data_json);
        $name = $data->name;
        $author = Author::where('id','=',$id)->get();
        if(count($author)==0){
            return response(json_encode(['message'=>'Author does not exist'],JSON_UNESCAPED_SLASHES),404);
        }else{
            $author = $author->first();
            $author->name =  $name;
            $author->save();
            return response(json_encode($author),200);
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
        $author = Author::where('id','=',$id)->get();
        if(count($author)==0){
            return response(json_encode(['message'=>'Author does not exist'],JSON_UNESCAPED_SLASHES),404);
        }
        $author = $author->first();
        $books = $author->books;
        if(count($books)!==0){
            $booksInfo = [];
            foreach ($books as $book){
                array_push($booksInfo,$book->getBookLinks());
            }
            return response(json_encode(['message'=>'Author has stored books, delete books before the author', 'books'=>$booksInfo],JSON_UNESCAPED_SLASHES),400);
        }
        $author->delete();
        return response(json_encode(['message'=>'Author '.$id.' succsessfuly deleted.'],JSON_UNESCAPED_SLASHES),200);
    }

    public function find($term)
    {   $authors = Author::where('name','like','%'.$term.'%')->get();
        if(count($authors)!==0){
            $data = new stdClass();
            $data->authors = [];
            $data->_links = ['_self' =>'http://library-assignment.filipivanko.com/api/authors/find/'.$term];
            foreach ($authors as $author) {
                array_push($data->authors, $author->getAuthorInfo('author_profile'));
            }
            return  response(json_encode($data,JSON_UNESCAPED_SLASHES),200);
        }else{
            return  response(json_encode(['message'=>'No records found matching the search term'],JSON_UNESCAPED_SLASHES),404);
        }
    }

}
