# How to autoload


    require_once '../vendor/autoload.php';


    
# example

    session_start();

    include('src/Quote.php');
    include('src/QuoteController.php');
    include('src/QuoteHelper.php');

    $option = array();
    $option['fields'] = array(
        'name'=>array('required'),
        'country-coverage'=>array('required'),
        'nationality'=>array('required'),
        'email'=>array('required','email'),
    );
    $option['default'] = array(
            'source' => 'RHI',
            'type' => 'Individual',
            'comments' => array(1,2,3)
    );
    
    $quote = new Quote($option);
    $controller = new QuoteController($quote);
    $controller->firstStep('personalize-search.php');

    $quote->hasError('name');
    echo($quote['name']);
   
#AB Test
    use PP\Common\QuoteAB;
    session_start();
    include('src/QuoteAB.php');

    $configAB = array(
        'A'=>'version A.php',
        'B'=>'version B.php',
    );

    $abTest = new QuoteAB('ab.json', $configAB,'dadf');

    echo($abTest->getVersion());

