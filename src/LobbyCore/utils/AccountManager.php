<?php

namespace LobbyCore\utils;


class AccountManager
{

    /** @var MySQLManager $sqlManager */
    private $sqlManager;

    /**
     * AccountManager constructor.
     * @param MySQLManager $mySQLManager
     */
    public function __construct(MySQLManager $mySQLManager)
    {
        $this->sqlManager = $mySQLManager;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function isPlayerRegistred(string $username) : bool{
        return $this->sqlManager->fetchArrayByKey("players", "username", $username) !== null;
    }

    /**
     * @param string $username
     */
    public function registerPlayer(string $username) : void{
        $registerData = [
            'defaultServer' => 'skywars',
            'coins' => 0,
            'rank' => 'Guest',
            'username' => $username
        ];

        $query = "INSERT INTO players (defaultServer, coins, rank, username) VALUES('skywars', '0', 'Guest', '$username')";
        $this->sqlManager->getMysqli()->query($query);
    }

    /**
     * @param string $username
     * @return array
     */
    public function getPlayerData(string $username) : ?array{
        return $this->sqlManager->fetchArrayByKey("players", "username", $username);

    }

    /**
     * @param string $username
     * @param string $rank
     */
    public function setRank(string $username, string $rank) : void{
        $command = "UPDATE players SET rank='$rank' WHERE username='$username'";
        $this->sqlManager->getMysqli()->query($command);
    }

    /**
     * @param string $username
     * @param string $server
     */
    public function setDefaultServer(string $username, string $server) : void{
        $command = "UPDATE players SET defaultServer='$server' WHERE username='$username'";
        $this->sqlManager->getMysqli()->query($command);
    }



}