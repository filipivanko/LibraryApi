<?php

namespace App\Models;
use App\Models\Author;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Book extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'books';
    public $timestamps = false;



    public function getauthor(){
        return $this->belongsTo('App\Models\Author','author_id','id');
    }

    public function getBookInfo( $bookProvileKey = 'book_profile'){
        $author = $this->getauthor;
        $data = new stdClass();
        $data->title = $this->title;
        $data->author = $author;
        $data->copies_available = $this->copies_available;
        $data->_links = [];
        $data->_links[$bookProvileKey] = 'http://library-assignment.filipivanko.com/api/books/'.$this->id;
        $data->_links['author_profile'] = 'http://library-assignment.filipivanko.com/api/authors/'.$author->id;

        return $data;
    }
    public function getBookLinks(){
        $data = new stdClass();
        $data->title = $this->title;
        $data->_links['book_profile'] = 'http://library-assignment.filipivanko.com/api/books/'.$this->id;


        return $data;
    }
}
