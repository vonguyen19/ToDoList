<?php
require_once('connection.php');

class Work
{
    public $workName;
    public $startDate;
    public $endDate;
    public $status;

    function __construct($workName, $startDate, $endDate, $status)
    {
        $this->workName = $workName;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->status = $status;
    }

    static function create($workName, $startDate, $endDate, $status){
        $db = DB::getInstance();
        $sqlCreate = " INSERT INTO works (name, start_date, end_date, status) VALUES (:workName, :startDate, :endDate, :stt)";

        $stmt = $db->prepare($sqlCreate);
        $stmt->bindParam(':workName', $workName);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':stt', $status);
        $stmt->execute();
        $lastInsertId = $db->lastInsertId();
        return $lastInsertId;
    }

    static function all()
    {
        $listWork = [];
        $db = DB::getInstance();
        $req = $db->query('SELECT * FROM works');
        $listWork = $req->fetchAll();
        // foreach ($req->fetchAll() as $item) {
        //     $list[] = new Post($item['id'], $item['title'], $item['content']);
        // }

        return $listWork;
    }
}
