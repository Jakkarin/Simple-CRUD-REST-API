<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('response');
        $this->load->model('product_model');
    }

    public function index()
	{
        $current_page = $_GET['p'] ?? 1;

        $responseData = [];
        $responseData['items'] = [];
        $responseData['count'] = $this->product_model->count_all();
        $responseData['limit'] = 5;
        $responseData['total_page'] = ceil($responseData['count']/$responseData['limit']);
        $responseData['current_page'] = intval($current_page) ?? 1;

        $this->product_model->paginate($responseData);

		$this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($responseData));
    }
    
    public function store()
	{
        try {
            $input = $this->product_model->validateInput();
        } catch (Exception $e) {
            return responseJSONError($this->output, 400, [
                'code' => 'BAD_REQUEST',
                'text' => $e->getMessage(),
            ]);
        }

        $insertID = $this->product_model->insert($input);

        if (empty($insertID)) {
            return responseJSONError($this->output, 500, [
                'code' => 'SERVER_ERROR',
                'text' => 'เกิดข้อผิดพลาดไม่สามารถเชื่อมต่อฐานข้อมูลได้'
            ]);
        }

        $input['prodid'] = $insertID;

        return responseJSON($this->output, $input);
    }
    
    public function update($prodID)
	{
        if ( ! is_numeric($prodID)) {
            return responseJSONError($this->output, 400, [
                'code' => 'BAD_REQUEST',
                'text' => 'รหัสสินค้าไม่ถูกต้อง',
            ]);
        }

        try {
            $input = $this->product_model->validateInput();
        } catch (Exception $e) {
            return responseJSONError($this->output, 400, [
                'code' => 'BAD_REQUEST',
                'text' => $e->getMessage(),
            ]);
        }

        $product = $this->product_model->one($prodID);

        if (empty($product)) {
            return responseJSONError($this->output, 500, [
                'code' => 'NOT_FOUND',
                'text' => 'ไม่พบสินค้าที่ต้องการ'
            ]);
        }

        $this->product_model->update($prodID, $input);

        return responseJSON($this->output, $input);
    }
    
    public function delete($prodID)
	{
        if ( ! is_numeric($prodID)) {
            return responseJSONError($this->output, 400, [
                'code' => 'BAD_REQUEST',
                'text' => 'รหัสสินค้าไม่ถูกต้อง',
            ]);
        }

        $product = $this->product_model->delete($prodID);
        return responseJSON($this->output, ['text' => $prodID]);
	}
}
