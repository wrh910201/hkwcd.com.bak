<?php

class SitemapAction extends CommonAction {



	public function index(){
		$this->type = '生成sitemap'; 
		$this->display();
	}
	
	
	
	public function go(){
		 
		$article = D('ArticleView')->where(array('article.status' => 0))->order('article.id DESC')->select();
		$picture = D('PictureView')->where(array('picture.status' => 0))->order('picture.id DESC')->select();
		$product = D('ProductView')->where(array('product.status' => 0))->order('product.id DESC')->select();
 
		$sitemap='<?xml  version="1.0" encoding="utf-8"?><urlset  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"   xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';
		$sitemap.='<url>';
		$sitemap.='<loc>http://www.hoxinit.com</loc>';
		$sitemap.='<title>宏信网络</title>'; 
		$sitemap.='<changefreq>Always</changefreq>';	
		$sitemap.='<priority>1.0</priority>';
		$sitemap.='</url>';
		
		foreach($article as $c){ 
			   $sitemap.='<url>';
			   $sitemap.='<loc>'.C('cfg_weburl').'Index/Show/article/id/'.$c['id'].'</loc>';
			   $sitemap.='<title>'.$c['title'].'</title>';
				if($in['litpic']){
				$sitemap.='<image:image><image:loc>'.C('cfg_weburl').$c['litpic'].'</image:loc></image:image>';
				}
			    $sitemap.='<rlastchange>'.$c['publishtime'].'</rlastchange>';
			    $sitemap.='<changefreq>Always</changefreq>';	
				$sitemap.='<priority>1.0</priority>';
				$sitemap.='</url>';
			}  
			
		foreach($picture as $c){ 
			$sitemap.='<url>';
			$sitemap.='<loc>'.C('cfg_weburl').'Index/Show/picture/id/'.$c['id'].'</loc>';
			$sitemap.='<title>'.$c['title'].'</title>';
			if($in['litpic']){
				$sitemap.='<image:image><image:loc>'.C('cfg_weburl').$c['litpic'].'</image:loc></image:image>';
			}
			$sitemap.='<rlastchange>'.$c['publishtime'].'</rlastchange>';
			$sitemap.='<changefreq>Always</changefreq>';	
			$sitemap.='<priority>1.0</priority>';
			$sitemap.='</url>';
		}  
		
		foreach($product as $c){ 
			$sitemap.='<url>';
			$sitemap.='<loc>'.C('cfg_weburl').'Index/Show/product/id/'.$c['id'].'</loc>';
			$sitemap.='<title>'.$c['title'].'</title>';
			if($in['litpic']){
				$sitemap.='<image:image><image:loc>'.C('cfg_weburl').$c['litpic'].'</image:loc></image:image>';
			}
			$sitemap.='<rlastchange>'.$c['publishtime'].'</rlastchange>';
			$sitemap.='<changefreq>Always</changefreq>';	
			$sitemap.='<priority>1.0</priority>';
			$sitemap.='</url>';
		}  
		
		$sitemap.='</urlset>';
		$confpath ='sitemap.xml';
		$webconfig = @fopen($confpath,w);
		if($webconfig){
			$fwrite=fwrite($webconfig,$sitemap);
			 
			$this->success('生成成功：',U(GROUP_NAME. '/Sitemap/index'));
		}else{
			$this->error('网站根目录没有写入权限！');
		} 
	}
	
		
}