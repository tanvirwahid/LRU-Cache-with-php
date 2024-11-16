<?php

class Node
{
    private ?Node $previousNode = null;
    private ?Node $nextNode = null;

    public function __construct(private $key, private $value)
    {}

    /**
     * @return Node
     */
    public function getPreviousNode(): ?Node
    {
        return $this->previousNode;
    }

    /**
     * @param Node $previousNode
     */
    public function setPreviousNode(Node $previousNode = null)
    {
        $this->previousNode = $previousNode;
    }

    /**
     * @return Node
     */
    public function getNextNode(): ?Node
    {
        return $this->nextNode;
    }

    /**
     * @param Node $nextNode
     */
    public function setNextNode(Node $nextNode = null)
    {
        $this->nextNode = $nextNode;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    public function __toString(): string
    {
        $next = 'null';
        $prev = 'null';

        if ($this->nextNode != null) {
            $next = $this->nextNode->getValue();
        }

        if ($this->previousNode != null) {
            $prev = $this->previousNode->getValue();
        }

        return 'Prev = ' . $prev .
            ' value = ' . $this->value .
            ' Next = ' . $next;
    }

}

class DoublyLinkedList
{

    private ?Node $head = null;
    private ?Node $tail = null;

    public function addToFront(Node $node)
    {
        $node->setNextNode($this->head);

        if ($this->head != null) {
            $this->head->setPreviousNode($node);
        }

        $this->head = $node;

        if($this->tail == null)
        {
            $this->tail = $node;
        }
    }

    public function delete(Node $node)
    {
        $previousNode = $node->getPreviousNode();
        $nextNode = $node->getNextNode();

        if ($previousNode != null) {
            $previousNode->setNextNode($nextNode);
        } else
        {
            $this->head = $nextNode;
        }

        if ($nextNode != null) {
            $nextNode->setPreviousNode($previousNode);
        } else
        {
            $this->tail = $previousNode;
        }

    }

    public function moveToFront(Node $node)
    {
        $previousNode = null;
        $this->delete($node);
        $this->addToFront($node);
        $node->setPreviousNode($previousNode);
    }

    public function getHead(): ?Node
    {
        return $this->head;
    }

    public function getTail(): ?Node
    {
        return $this->tail;
    }

}

class Cache
{
    private $hashMap = [];

    public function __construct(
        private DoublyLinkedList $doublyLinkedList,
        private int $capacity
    )
    {}

    public function get($key)
    {
        if(!isset($this->hashMap[$key]))
        {
            return null;
        }

        $node = $this->hashMap[$key];

        $this->doublyLinkedList->moveToFront($node);

        return $node->getValue();
    }

    public function put($key, $value)
    {
        if(isset($this->hashMap[$key]))
        {
            $node = $this->hashMap[$key];
            $node->setValue($value);
            $this->doublyLinkedList->moveToFront($node);
        }

        $node = new Node($key, $value);
        $this->doublyLinkedList->addToFront($node);
        $this->hashMap[$key] = $node;

        if(count($this->hashMap) > $this->capacity)
        {
            $tail = $this->doublyLinkedList->getTail();
            if($tail != null)
            {
                $this->doublyLinkedList->delete($tail);
                unset($this->hashMap[$tail->getKey()]);
            }
        }
    }

    public function forget($key)
    {
        if(!isset($this->hashMap[$key]))
        {
            return;
        }

        $node = $this->hashMap[$key];
        $this->doublyLinkedList->delete($node);
        unset($this->hashMap[$key]);
    }

    public function printKeyValuePairs()
    {
        $current = $this->doublyLinkedList->getHead();

        while($current != null)
        {
            echo 'Key = '. $current->getKey(). ' Value = '.$current->getValue();
            echo PHP_EOL;
            $current = $current->getNextNode();
        }
    }
}

$cache = new Cache(new DoublyLinkedList(), 3);

$cache->put(1, 3);
$cache->put(2, 4);
$cache->put(3, 5);

$cache->printKeyValuePairs();

echo $cache->get(1). PHP_EOL;
echo $cache->get(2). PHP_EOL;
echo $cache->get(3). PHP_EOL;
echo $cache->get(2). PHP_EOL;

$cache->printKeyValuePairs();
echo PHP_EOL;

$cache->forget(2);
$cache->printKeyValuePairs();
echo PHP_EOL;

$cache->put(2, 4);

$cache->printKeyValuePairs();
echo PHP_EOL;

$cache->put(4, -1);
$cache->printKeyValuePairs();
echo PHP_EOL;
