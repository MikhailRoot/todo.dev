<?php


namespace App;


use App\Http\Exceptions\DbException;

class Todo
{

    public $id=null;

    public $username;

    public $email;

    public $state;

    public $description;

    public $photo;


    /**
     * @var Db
     */
    protected $_db;

    protected $_tableName='Todos';

    public function __construct($id=null, $username=null, $email=null, $description=null , $state=null , $photo=null)
    {

        $this->_db=Db::getInstance();
        if(is_array( $id) ){

            // as id is our array containing data
            $this->fill($id);

        }elseif(is_numeric($id) && !is_null($id)){

            $this->fill($this->getDataById($id));

        }else{
            // fill in passed in values
            $this->id=null;
            $this->username = $username;
            $this->email = $email;
            $this->state = $state;
            $this->description = $description;
            $this->photo = $photo;
        }
    }

    private function fill($data)
    {
        foreach($data as $field=>$value)
        {
                $this->{$field}=$value;
        }
    }

    private function clear()
    {
        $fields=get_class_vars(__CLASS__);

        foreach($fields as $field=>$default_value)
        {
            if($field!=='_db'){
                $this->{$field}=$default_value;
            }

        }
    }

    public function toArray()
    {
        return (array)$this;
    }

    private function getDataById($id)
    {
        return $this->_db->Query("SELECT * FROM {$this->_tableName} WHERE `id`= ?  LIMIT 0,1;",[$id])->fetch(\PDO::FETCH_ASSOC);
    }


    public function save()
    {
        $SQL="";
        $inputs=[];
        if($this->id!==null){
            // make update
            $inputs=[
                $this->username,
                $this->email,
                $this->description,
                $this->state,
                $this->photo,
                $this->id
            ];

            $SQL="UPDATE {$this->_tableName} SET username= ? , email= ?, description=? , state= ? , photo= ?  WHERE id= ? ;";

        }else{
            $inputs=[
                $this->username,
                $this->email,
                $this->description,
                $this->state,
                $this->photo,
            ];
            $SQL="INSERT INTO {$this->_tableName} (id, username, email, description, state, photo) VALUES ( NULL , ? , ? , ? , ? , ? );";
        }

       $rowsAffected = $this->_db->Query( $SQL, $inputs )->rowCount();

        if( is_null($this->id) || intval($this->id)<1 && $rowsAffected==1 ){
                $this->id = $this->_db->getLastInsertId();
        }
            // reload updated or inserted data to make sure we are working with latest stored version.
        $this->fill( $this->getDataById($this->id) );


    }

    public function destroy()
    {
        $SQL="DELETE FROM {$this->_tableName} WHERE id= ? ;";

       if ( $this->id > 0  &&   $this->_db->Query($SQL,[$this->id])->rowCount()==1){

            $this->clear();

       }else{
           throw new DbException('SQL ERROR on DELETE with id='.$this->id);
       }

    }

    public function selectTodos($where=[], $orderBy=[], $limit=3, $offset=0)
    {
        $inputs=[];
        $SQL = "SELECT SQL_CALC_FOUND_ROWS  * FROM {$this->_tableName} WHERE  ";
        //validate wheres and orderby fields;
        $fieldsAvailiable=get_class_vars(__CLASS__);

        $realOrderby=[];
        foreach($orderBy as $field=>$value){
            if(array_key_exists($field,$fieldsAvailiable) && ($value==='ASC' || $value==='DESC')){
                $realOrderby[$field]=$value;
            }
        }
        $orderBy=$realOrderby;

        $realWheres=[];
        foreach($where as $tested){
            if(array_key_exists($tested['field'],$fieldsAvailiable)){
                $realWheres[]=$tested;
            }
        }
        $where=$realWheres;

        $count_where=count($where);
        if($count_where>0){

            foreach($where as $rule){
                $field_name = $rule['field'];
                $value = $rule['value'];
                $comparison = $rule['compare']??'=';

                array_push($inputs, $value);
                $SQL.=" `{$field_name}` {$comparison}  ? ";
                $count_where--;
                if($count_where>0){
                    $SQL.=" AND ";
                }
            }
        }else{
            $SQL.=" 1 ";
        }

        $count_orderBy=count($orderBy);
        if($count_orderBy>0){
            $SQL.=" ORDER BY ";
            foreach($orderBy as $field_name=>$direction)
            {
                $SQL.=" {$field_name} {$direction} ";
                $count_orderBy--;

                if($count_orderBy>0){
                    $SQL.=" , ";
                }
            }
        }
        // limiting

        $SQL.=" LIMIT {$offset}, {$limit} ;";

        $rows=$this->_db->Query($SQL,$inputs)->fetchAll(\PDO::FETCH_ASSOC);

        // TODO convert assoc array rows to object!
        $max_items_count=intval( $this->_db->Query("SELECT FOUND_ROWS();")->fetchColumn() );

        $currentPage=0;
        if($offset>0){
            $currentPage = intval( $offset/$limit );
        }

        $result=[
            'items'=>[], // see below we fill items in! as objects
            'pagination'=>[
                'offset'        => $offset,
                'limit'         => $limit,
                'currentPage'   => $currentPage+1,
                'maxItemsCount' => $max_items_count
            ]
        ];

        foreach($rows as $row){
            $todo=new Todo($row);
            $result['items'][]=$todo;
        }

        return $result;
    }

    public function CreateTable(){

        $sql="
            CREATE TABLE IF NOT EXISTS {$this->_tableName}
            (
                id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
                username varchar(255) DEFAULT '',
                email varchar(255),
                description text,
                state int DEFAULT 0,
                photo varchar(255) DEFAULT ''
            );
        ";

        $this->_db->Query($sql);
    }

}