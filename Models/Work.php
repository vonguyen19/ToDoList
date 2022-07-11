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

    static function create($workName, $startDate, $endDate, $status)
    {
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

    static function all($start, $end)
    {
        $listWork = [];
        try {
            $db = DB::getInstance();
            $sql = "SELECT * FROM works";
            if (!$start && !$end) {
                $sql = "SELECT * FROM works WHERE NOT ((end_date <= :start) OR (start_date >= :end)) ";
            }
            $stmt = $db->prepare($sql);
            if (!$start && !$end) {
                $stmt->bindParam(':start', $start);
                $stmt->bindParam(':end', $end);
            }
            $stmt->execute();
            foreach($stmt->fetchAll() as $row) {
                $listWork[] = [
                    'id' => $row['id'],
                    'text' => $row['name'],
                    'start' => $row['start_date'],
                    'end' => $row['end_date'],
                    'status' => $row['status'],
                    'backColor' => '#3d85c6',
                ];
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $listWork;
    }

    static function delete($workId)
    {
        $db = DB::getInstance();
        $sqlDel = " DELETE FROM works where id = $workId";

        $stmt = $db->prepare($sqlDel);
        $stmt->execute();
    }

    static function update($workId, $workName, $startDate, $endDate, $status)
    {
        $db = DB::getInstance();
        $sqlUpdate = " UPDATE works SET name = :workName, start_date = :startDate, end_date = :endDate, status = :stt  WHERE id = $workId";
        $stmt = $db->prepare($sqlUpdate);
        $stmt->bindParam(':workName', $workName);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':stt', $status);
        $stmt->execute();
    }
}
