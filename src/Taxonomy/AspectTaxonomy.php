<?php


namespace Taxonomy;

use Entity\Omen\OmenCollection;
use Taxonomy\Term;

class AspectTaxonomy extends Taxonomy
{


    /**
     * @param $slug
     * @param $title
     *
     * Creates a new Taxonomony and adds it to the taxonomies array
     */
    public function createTerm($slug, $title) : Aspect
    {
        return (new Aspect())
            ->setId(count($this->terms))
            ->setSlug($slug)
            ->setTitle($title);
    }

    //TODO: @Sam - confirm whether this change is okay. Original code is commented out
    public function addTerm(Term $aspect): array//Aspect $aspect): array
    {
        $this->terms[] = $aspect;
        return $this->terms;
    }

    /**
     * @param string $slug
     * @return Aspect
     *
     * This returns a Aspect object based on a slug passed to it
     *
     * TODO: error handling
     * TODO: move to parent class
     */
    public static function getTermBySlug(string $slug): Aspect
    {
        /*
        foreach ((new self)->loadArray()->terms as $taxonomy){
            if ($taxonomy->getSlug() == $slug) return $taxonomy;
        }*/

        $connection = mysqli_connect(self::DBHOST, self::DBUSER, self::DBPASS, self::DBNAME);

        // Test if connection succeeded
        if(mysqli_connect_errno()) { die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")" ); }

        // 2. Perform database query
        $query = "SELECT * ";
        $query .= 'FROM '.self::T_ASPECT;
        //Filter result by slug
        $query .= " WHERE ".self::T_ASPECT.".".self::C_SLUG." = '".$slug."';";

        $result = mysqli_query($connection, $query);

        // 3. Use returned data
        $output;
        while($row = mysqli_fetch_array($result))
        {
            //print_r($row);

            $output = (new Aspect())
            ->setId($row[0])
            ->setSlug($row[1])
            ->setTitle($row[2]);
        }

        // 4. Release returned data
        mysqli_free_result($result);
  
        // 5. Close database connection
        mysqli_close($connection);

        return $output;
    }

    public static function getAllTerms(): array
    {
        //TODO: database query
        //SQL query
        // should use "(new self)" which will call the constructor
        // (which sets up the db connection)
        // don't forget to close the database connection "$this->connection = null"
        // return an array of Term objects
        
        //TODO: MOVE TO CONSTRUCTOR
        //new self();

        //TMP
        // Set up MySQLi connection
        // Code for connection is from Lab.
        // 1. Create a database connection
        $connection = mysqli_connect(self::DBHOST, self::DBUSER, self::DBPASS, self::DBNAME);

        // Test if connection succeeded
        if(mysqli_connect_errno()) { die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")" ); }

        // 2. Perform database query
        $query = "SELECT * ";

        $query .= 'FROM '.self::T_ASPECT;

        $result = mysqli_query($connection, $query);

        // 3. Use returned data
        //Print querie
        //echo $query;
        //prepare array
        $output = array();
        //Print rows
        while($row = mysqli_fetch_array($result))
        {
            //print_r($row);

            $item = (new Fault())
            ->setId($row[0])
            ->setSlug($row[1])
            ->setTitle($row[2]);

            array_push($output, $item);
        }



        // 4. Release returned data
        mysqli_free_result($result);
  
        // 5. Close database connection
        mysqli_close($connection);

        return $output;
    }

    public function getTerms(): array
    {
        //TODO: This should be different from getAllTerms
        //TODO: database query
        //SQL query
        // don't forget to close the database connection "$this->connection = null"
        // return an array of Term objects

        return self::getAllTerms();
    }
}