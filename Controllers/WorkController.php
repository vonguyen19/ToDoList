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
        $works = Work::all();
        // var_dump($works); exit;
        $this->render('index', $works);
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
    }

    public function delete()
    {
    }

    public function error()
    {
        $this->render('error');
    }
}
