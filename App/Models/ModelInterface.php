<?php namespace Carbontwelve\InboundTracker\Models;


interface ModelInterface
{

    public function install();

    public function getAll();

    public function update($id = null, Array $data);

    public function insert(Array $data);

}
