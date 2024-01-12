<?php
namespace App\Models\Bpjskes\Vclaim\V2;
class Referensi extends BaseVclaim
{
    public $data;
    function matchByName($obj)
    {
        $tmp=NULL;
        if(count($this->data)>0){
            foreach($this->data as $d){
                $x=explode('-',$d['nama']);
                if(count($x)==2){
                    if(trim($x[1])==trim($obj)){
                        $tmp=$d;
                        break;
                    }
                }else{
                    if(trim($d['nama'])==trim($obj)){
                        $tmp=$d;
                        break;
                    }
                }
            }
        }
        return $tmp;
    }
    function diagnosa($diagnosa)
    {
        if($this->setup([
            'url'=>'referensi/diagnosa/'.trim($diagnosa),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['diagnosa'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function poliklinik($poli)
    {
        if($this->setup([
            'url'=>'referensi/poli/'.trim($poli),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['poli'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }

    }
    function faskes($jenis,$faskes)
    {
        if($this->setup([
            'url'=>'referensi/faskes/'.trim($faskes).'/'.trim($jenis),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['faskes'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama),'tingkat'=>trim($jenis)];
            }
            return true;
        }else{
            return false;
        }
    }
    function dpjp($req)
    {
        $tmp=explode('!#!',$req['spesialis']);

        $spesialis=NULL;
        if(count($tmp)==2){
            $spesialis=$tmp[0];
        }else{
            $this->error_msg="Format spesialis tidak valid";
            return false;
        }

        $tgl_layanan=date('Y-m-d');

        if(isset($req['tgl_layanan'])){
            $tgl_layanan=date('Y-m-d',strtotime($req['tgl_layanan']));
        }

        if($this->setup([
            'url'=>'referensi/dokter/pelayanan/'.trim($req['jenis_layanan']).'/tglPelayanan/'.$tgl_layanan.'/Spesialis/'.trim($spesialis),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function provinsi()
    {
        if($this->setup([
            'url'=>'referensi/propinsi',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function kabupaten($provinsi)
    {
        $provinsi=explode('!#!',$provinsi);
        if($this->setup([
            'url'=>'referensi/kabupaten/propinsi/'.trim($provinsi[0]),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function kecamatan($kabupaten)
    {
        $kabupaten=explode('!#!',$kabupaten);
        if($this->setup([
            'url'=>'referensi/kecamatan/kabupaten/'.trim($kabupaten[0]),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function diagnosaprb()
    {
        if($this->setup([
            'url'=>'referensi/diagnosaprb',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function generikprb($obat)
    {
        if($this->setup([
            'url'=>'referensi/obatprb/'.trim($obat),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function tindakan($tindakan)
    {
        if($this->setup([
            'url'=>'referensi/procedure/'.trim($tindakan),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['procedure'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function kelasrawat()
    {
        if($this->setup([
            'url'=>'referensi/kelasrawat',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function dokter($dokter)
    {
        if($this->setup([
            'url'=>'referensi/dokter/'.trim($dokter),
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function spesialistik()
    {
        if($this->setup([
            'url'=>'referensi/spesialistik',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function ruangrawat()
    {
        if($this->setup([
            'url'=>'referensi/ruangrawat',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function carakeluar()
    {
        if($this->setup([
            'url'=>'referensi/carakeluar',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
    function pascapulang()
    {
        if($this->setup([
            'url'=>'referensi/pascapulang',
            'method'=>'GET'
        ])->run()){
            $tmp=(array) $this->getResponse();
            foreach($tmp['list'] as $q){
                $this->data[]=['id'=>trim($q->kode).'!#!'.trim($q->nama),'nama'=>trim($q->nama)];
            }
            return true;
        }else{
            return false;
        }
    }
}