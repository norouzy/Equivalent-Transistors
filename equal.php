<?php
include_once 'requests/Requests.php';
include_once 'scraper/simple_html_dom.php';
$input = "Tmpth81";
class equal{
    public function equals($q){
        Requests::register_autoloader();
        $main_url = "https://alltransistors.com/search.php?search=$q";
        $headers = array('Accept' => 'application/json');

        $request = Requests::get($main_url, $headers);
        $response = $request->body;
        $html = str_get_html($response);
        $uls=$html->find("ul[class=leaders]");
        if($uls[0]->innertext==''){
            return ["title"=>"","json"=>"","status"=>false];
        }
        $a=$uls[0]->find("li")[0]->find("span")[0]->find("a");
        $page2 = $a[0]->href;




        $request = Requests::get($page2, $headers);
        $response = $request->body;
        $html = str_get_html($response);
        $page3=$html->find("div[id=content2]")[0]->find("p")[0]->find("a")[0]->href;
        $request = Requests::get($page3, $headers);
        $response = $request->body;
        $html = str_get_html($response);
        $table = $html->find("table[class=sort]")[0];
        if($table->innertext==''){
            return ["title"=>"","json"=>"","status"=>false];
        }
        $TabletTitle=$table->find("thead")[0]->find("tr[align=left]")[0]->find("td");

        $Title = [];
        foreach ($TabletTitle as $val){
            $text=str_replace("&nbsp;","",$val->innertext);
            array_push($Title,$text);

        }

        $trs = $html->find("table[class=sort]")[0]->find("tbody")[0]->find("tr");
        $data =[];
        foreach ($trs as $tr){
            $tds=$tr->find("td");
            $detail =[];
            foreach ($tds as $td){

                $text=str_replace("&nbsp;","",$td->plaintext);
                array_push($detail,trim($text));

            }
            array_push($data,[$detail]);
        }
        $data = json_encode($data);
        return ["title"=>$Title,"json"=>$data,"status"=>true];

    }

	public function make_json($data){
		if($data['status']){
			$titles = $data['title'];
			$total_data = array();
			foreach (json_decode($data['json']) as $value){
				$row=array_combine($titles,$value[0]);
				array_push($total_data,$row);
			}
			echo(json_encode($total_data));

		}
	}

}

$eq = new equal();
$data=$eq->equals($input);
$eq->make_json($data);











