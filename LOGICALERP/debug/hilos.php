<?php
// Thread extends Threaded implements Countable , Traversable , ArrayAccess {
// /* Methods */
// public void detach ( void )
// public integer getCreatorId ( void )
// public static Thread getCurrentThread ( void )
// public static integer getCurrentThreadId ( void )
// public integer getThreadId ( void )
// public static mixed globally ( void )
// public boolean isJoined ( void )
// public boolean isStarted ( void )
// public boolean join ( void )
// public void kill ( void )
// public boolean start ([ integer $options ] )
// /* Inherited methods */
// public array Threaded::chunk ( integer $size , boolean $preserve )
// public integer Threaded::count ( void )
// public bool Threaded::extend ( string $class )
// public Threaded Threaded::from ( Closure $run [, Closure $construct [, array $args ]] )
// public array Threaded::getTerminationInfo ( void )
// public boolean Threaded::isRunning ( void )
// public boolean Threaded::isTerminated ( void )
// public boolean Threaded::isWaiting ( void )
// public boolean Threaded::lock ( void )
// public boolean Threaded::merge ( mixed $from [, bool $overwrite ] )
// public boolean Threaded::notify ( void )
// public boolean Threaded::pop ( void )
// public void Threaded::run ( void )
// public mixed Threaded::shift ( void )
// public mixed Threaded::synchronized ( Closure $block [, mixed $... ] )
// public boolean Threaded::unlock ( void )
// public boolean Threaded::wait ([ integer $timeout ] )
// }

class STD extends Threaded{
    public function put(){

        $this->synchronized(function(){
            for($i=0;$i<7;$i++){

    printf("%d\n",$i);
    $this->notify();
    if($i < 6)
    $this->wait();
    else
        exit();
    sleep(1);
}
        });

    }

        public function flush(){

$this->synchronized(function(){
            for($i=0;$i<7;$i++){
    flush();
    $this->notify();
    if($i < 6)
    $this->wait();
    else
        exit();
    }
});
}
}

class A extends Thread{
    private $std;
    public function __construct($std){
        $this->std = $std;
    }
    public function run(){
        $this->std->put();
    }
}

class B extends Thread{
    private $std;
    public function __construct($std){
        $this->std = $std;
    }
    public function run(){
        $this->std->flush();
    }
}
ob_end_clean();
echo str_repeat(" ", 1024);
$std = new STD();
$ta = new A($std);
$tb = new B($std);
$ta->start();
$tb->start();


?>