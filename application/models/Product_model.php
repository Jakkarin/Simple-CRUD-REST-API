<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    public $table = 'products';
    public $primaryKey = 'prodid';

    public function all()
	{
        return $this->db
            ->get($this->table)
            ->result_array();
    }

    public function count_all()
	{
        return $this->db->count_all_results($this->table);
    }

    public function paginate(&$data)
    {
        $data['items'] = $this->db
            ->limit($data['limit'], ($data['current_page'] - 1) * $data['limit'])
            ->get($this->table)
            ->result_array();
    }

    public function one($id)
	{
        return $this->db
            ->where($this->primaryKey, $id)
            ->get($this->table)
            ->row_array();
    }

    public function find($id)
	{
        return $this->db
            ->where($this->primaryKey, $id)
            ->get($this->table)
            ->result_array();
    }
    
    public function insert($data)
	{
        $this->db->insert($this->table, $data);
        return  $this->db->insert_id();
    }
    
    public function update($id, $data)
	{
		$this->db->update($this->table, $data, [
            "$this->primaryKey" => $id
        ]);
    }
    
    public function delete($id)
	{
        $this->db->delete($this->table, [$this->primaryKey => $id]);
    }
    
    public function validateInput()
    {
        $data = json_decode(file_get_contents('php://input'), true);

        $name = $data['name'];
        $price = $data['price'];
        $qty = $data['qty'];
        $unit = $data['unit'];

        if (empty($name)) {
            throw new Exception("กรุณาป้อนชื่อสินค้า");
        }

        if (empty($price)) {
            throw new Exception("กรุณาป้อนราคาสินค้า");
        }

        if ( ! is_numeric($price)) {
            throw new Exception("ราคาสินค้าต้องเป็นตัวเลขเท่านั้น");
        }

        if (empty($qty)) {
            throw new Exception("กรุณาป้อนจำนวนสินค้า");
        }

        if ( ! is_numeric($qty)) {
            throw new Exception("จำนวนสินค้าต้องเป็นตัวเลขเท่านั้น");
        }

        if (empty($unit)) {
            throw new Exception("กรุณาป้อนหน่วยสินค้า");
        }

        return [
            'prodname' => $name,
            'prodprice' => $price,
            'prodqty' => $qty,
            'produnit' => $unit,
        ];
    }
}
