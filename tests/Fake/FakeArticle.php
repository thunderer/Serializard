<?php
namespace Thunder\Serializard\Tests\Fake;

final class FakeArticle
{
    private $id;
    private $title;
    private $user;
    private $tag;

    public function __construct($id, $title, FakeUser $user, FakeTag $tag)
    {
        $this->id = $id;
        $this->title = $title;
        $this->user = $user;
        $this->tag = $tag;
    }

    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getUser() { return $this->user; }
    public function getTag() { return $this->tag; }
}
