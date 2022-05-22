<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Author extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'authors';
    protected $guarded = [];

    public function books(){
        return $this->hasMany(Book::class, 'author_id','id');
    }

    public function getAuthorInfo($authorProfileKey = 'author_profile'){
        $booksByAuthor = Book::all()->where('author_id','=',$this->id);
        $data = new stdClass();
        $data->name = $this->name;
        $data->booksByAuthor= [];
        foreach($booksByAuthor as $book){
            array_push($data->booksByAuthor, $book->getBookInfo());
        }

        $data->_links = [];
        $data->_links[$authorProfileKey] = 'http://library-assignment.filipivanko.com/api/authors/'.$this->id;
        return $data;
    }
}
