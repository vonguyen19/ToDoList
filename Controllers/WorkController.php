<?php
require_once('Controllers/BaseController.php');
require_once('Models/Work.php');

class WorkController extends BaseController
{
    function __construct()
    {
        $this->folder = 'works';
    }

    public function index()
    {
        $this->render('index');
    }

    public function show()
    {
        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';
        $works = Work::all($start, $end);
        echo json_encode($works);
    }


    public function add()
    {
        $this->render('add');
    }

    public function store()
    {
        $result = [
            'message' => 'success',
            'lastInsertId' => 0
        ];

        $workName = $_POST['workName'] ?? '';
        $startDate = $_POST['startDate'] ?? '';
        $endDate = $_POST['endDate'] ?? '';
        $status = $_POST['status'] ?? '';
        try {
            $lastInsertId = Work::create($workName, $startDate, $endDate, $status);
            $result['lastInsertId'] = $lastInsertId;
        } catch (Exception $e) {
            $result['message'] = 'fail';
        }

        echo json_encode($result);
    }

    public function update()
    {
        $result = [
            'status' => 'success',
            'message' => 'Completed update this work',
        ];

        $workId = $_POST['id'] ?? 0;
        $workName = $_POST['workName'] ?? '';
        $startDate = $_POST['startDate'] ?? '';
        $endDate = $_POST['endDate'] ?? '';
        $status = $_POST['status'] ?? '';
        if($workId > 0){
            try {
                Work::update($workId, $workName, $startDate, $endDate, $status);
            } catch (Exception $e) {
                $result['status'] = 'fail';
                $result['message'] = 'Can not update this work';
            }
        }else{
            $result['status'] = 'fail';
            $result['message'] = 'This work not found';
        }
        

        echo json_encode($result);
    }

    public function delete()
    {
        $result = [
            'status' => 'success',
            'message' => 'Success delete this work'
        ];
        $workId = $_POST['id'] ?? 0;
        if ($workId > 0) {
            try {
                Work::delete($workId);
            } catch (Exception $e) {
                $result['status'] = 'fail';
                $result['message'] = 'Something was wrong';
            }
        } else {
            $result['status'] = 'fail';
            $result['message'] = 'This work not found';
        }

        echo json_encode($result);
    }

    public function error()
    {
        $this->render('error');
    }
}
