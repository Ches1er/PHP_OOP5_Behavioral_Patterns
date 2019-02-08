<?php
        //Iterator

class Page{
    private $num;
    private $text;

    public function __construct($num, $text)
    {
        $this->num = $num;
        $this->text = $text;
    }

    public function getNum()
    {
        return $this->num;
    }

    public function getText()
    {
        return $this->text;
    }
}

class Book{
    private $pages = [];

    public function __construct()
    {
        $this->pages[]=new Page(1,"hello");
        $this->pages[]=new Page(2,"world");
        $this->pages[]=new Page(3,"!!!");
    }

    public function getPages(): array
    {
        return $this->pages;
    }
}

class BookIterator implements Iterator{
    private $index=0;
    private $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }


    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->book->getPages()[$this->index];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->book->getPages()[$this->index]->getNum();
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->index<count($this->book->getPages());
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index=0;
    }
}

/*$i = new BookIterator(new Book());
$i->next();
echo $i->current()->getText();
$i->rewind();

foreach ($i as $page){
    echo $page->getText()."\n";
}*/

        //Стратегия

$UserCommercial = [
    "id"=>"1",
    "name"=>"Vasia",
    "surname"=>"Pupkin",
    "company"=>"Co",
    "stage"=>"20",
    "type"=>"com"
];
$UserNative = [
    "id"=>"1",
    "name"=>"Vasia",
    "surname"=>"Pupkin",
    "age"=>"20",
    "type"=>"nat"
];

interface Strategy{
    public function getStrategyQuery();
}

class CommercialStrategy implements Strategy {

    private $data;

    public function __construct($data){
        $this->data = $data;
    }

    public function getStrategyQuery(){
        $q = "INSERT INTO `com_user` (`user_id`,`company_name`,`stage`) 
                VALUES (\"{$this->data['id']}\",\"{$this->data['company']}\",
                \"{$this->data['stage']}\")";
        return $q;
    }
}

class NativeStrategy implements Strategy{

    private $data;

    public function __construct($data){
        $this->data = $data;
    }
    public function getStrategyQuery(){
        $q = "INSERT INTO `com_user` (`user_id`,`age`) 
                VALUES (\"{$this->data['id']}\",\"{$this->data['age']}\")";
        return $q;
    }
}

class ChooseStrategy{
    private $strategy=[];
    private $data;

    public function __construct($data)
    {
        $this->strategy = [
            "com"=>new CommercialStrategy($this->data),
            "nat"=>new NativeStrategy($this->data),
        ];
    }

    public function getStrategy(): array
    {
        return $this->strategy;
    }
}

class InsertData{
    private $user;
    private $strategy;
    public function __construct($data)
    {
        $this->user = $data;
        $this->strategy = new ChooseStrategy($data);
    }
    public function createQuery(){
        $q = "INSERT INTO `user` (`user_id`,`user_name`,`user_surname`) 
                VALUES (\"{$this->user['id']}\",\"{$this->user['name']}\",
                \"{$this->user['surname']}\")\n";
        echo $q;
        $choose_s = $this->strategy->getStrategy()[$this->user['type']];
        $run_s = new $choose_s($this->user);
        echo $run_s->getStrategyQuery();
    }
}

/*$q = new InsertData($UserNative);
$q->createQuery();*/

        //State

interface Process{
    public function do();
}

Class ProcessHello implements Process {

    public function do()
    {
        echo "Hello\n";
    }
}

class ProcessWorld implements Process{

    public function do()
    {
        echo "World\n";
    }
}

class StateProcess implements Process {

    private $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function setProcess(Process $process): void
    {
        $this->process = $process;
    }

    public function do()
    {
        $this->process->do();
    }
}

class ProcessWorker{
    private $process;

    public function __construct(Process $process)
    {
        $this->process = $process;
    }

    public function work(){
        $this->process->do();
    }
}

/*$s = new StateProcess(new ProcessHello());
$p = new ProcessWorker($s);
$i=0;
while ($i<8){
    sleep(1);
    if ($i++>=4)$s->setProcess(new ProcessWorld());
    $p->work();
}*/

        //Observer

interface Observer{
    public function onNext($data);
}

abstract class Observable{
    private $subscribers = [];

    public function addToSubscribers(Observer $subscriber){
        $this->subscribers[] = $subscriber;
    }
    public function SubsAction($data){
        foreach ($this->subscribers as $subscriber) {
            $subscriber->onNext($data);
        }
    }
}

class Youtuber extends Observable {
    private $video;

    public function __construct($video)
    {
        $this->video = $video;
    }
    public function addVideo(){
        $this->SubsAction($this->video);
    }
}

class Subscriber implements Observer{

    public function onNext($data)
    {
        echo "New video $data\n";
    }
}

/*$subscribers = [new Subscriber(),new Subscriber(),new Subscriber()];
$obs = new Youtuber("Xaxa");
foreach ($subscribers as $subscriber){
    $obs->addToSubscribers($subscriber);
}
$obs->addVideo();*/
