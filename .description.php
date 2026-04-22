<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => "Новости",
	"DESCRIPTION" => GetMessage("IBLOCK_NEWS_DESCRIPTION"),
	"ICON" => "/images/news_all.gif",
	"COMPLEX" => "Y",
	"PATH" => array(
		"ID" => "my.components",
		"NAME" => "Мои компоненты",
		"CHILD" => array(
			"ID" => "news.component",
			"NAME" => "Новости тест",
			"SORT" => 10
		),
	),
);

?>