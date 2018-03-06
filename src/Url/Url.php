<?php
namespace webignition\Url;

class Url {
    
    /**
     *
     * @var \webignition\Url\Parser
     */
    private $parser = null; 
    
    
    /**
     *
     * @var \webignition\Url\Configuration
     */
    private $configuration = null;
    
    
    /**
     * Original unmodified source URL
     * 
     * @var string
     */
    private $originUrl = '';
    
    /**
     * Component parts of this URL, with keys:
     * -scheme
     * -host
     * -port
     * -user
     * -pass
     * -path
     * -query - after the question mark ?
     * -fragment - after the hashmark #
     * 
     * @var array
     */
    private $parts = null;    
    
    
    /**
     *
     * @var array
     */
    private $offsets = null;
    
    /**
     *
     * @var array
     */
    private $availablePartNames = array(
        'scheme',
        'user',
        'pass',
        'host',
        'port',        
        'path',
        'query',
        'fragment'
    );
    
    
    /**
     *
     * @param string $originUrl 
     */
    public function __construct($originUrl = null) {
        $this->init($originUrl);
    }
    
    
    /**
     * 
     * @param string $originUrl
     */
    public function init($originUrl) {
        $this->originUrl = $originUrl;
        $this->reset();
    }
    
    
    /**
     *
     * @return string 
     */
    public function getRoot() {
        $rawRootUrl = '';
        
        if ($this->hasScheme()) {
            $rawRootUrl .= $this->getScheme() . ':';
        }
        
        if ($this->hasHost()) {
            $rawRootUrl .= '//';

            if ($this->hasCredentials()) {
                $rawRootUrl .= $this->getCredentials() . '@';
            }
            
            $host = (string)$this->getHost();
            
            if ($this->getConfiguration()->getConvertIdnToUtf8()) {
                $host = \Etechnika\IdnaConvert\IdnaConvert::decodeString($host);
            }
            
            $rawRootUrl .= $host;
        }        
        
        if ($this->hasPort()) {
            $rawRootUrl .= ':' . $this->getPort();
        }

        return $rawRootUrl;
    }
    
    
    /**
     *
     * @return array
     */
    protected function &parts() {
        if (is_null($this->parts)) {
            $this->parts = $this->getParser()->getParts();
        }
        
        return $this->parts;
    }
    
    /**
     *
     * @return boolean
     */
    public function hasScheme() {
        return $this->hasPart('scheme');
    }
    
    
    /**
     *
     * @return string
     */
    public function getScheme() {
        return $this->getPart('scheme');
    }
    
    
    /**
     *
     * @param string $scheme 
     */
    public function setScheme($scheme) {
        $this->setPart('scheme', $scheme);
    }

    
    /**
     *
     * @return boolean
     */    
    public function hasHost() {
        return $this->hasPart('host');
    }
    
    
    /**
     *
     * @return \webignition\Url\Host\Host
     */
    public function getHost() {
        return $this->getPart('host');
    }
    
    /**
     *
     * @param string $host 
     */
    public function setHost($host) {
        $this->setPart('host', $host);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasPort() {
        return $this->hasPart('port');
    }
    
    
    /**
     *
     * @return int
     */
    public function getPort() {
        return $this->getPart('port');
    }
    
    
    /**
     *
     * @param int $port 
     */
    public function setPort($port) {
        $this->setPart('port', $port);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasUser() {
        return $this->hasPart('user');
    }
    
    
    /**
     *
     * @return string
     */
    public function getUser() {
        return $this->getPart('user');
    }
    
    
    /**
     *
     * @param string $user 
     */
    public function setUser($user) {
        $this->setPart('user', $user);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasPass() {
        return $this->hasPart('pass');
    }
    
    
    /**
     *
     * @return string
     */
    public function getPass() {
        return $this->getPart('pass');
    }
    
    
    /**
     *
     * @param string $pass 
     */
    public function setPass($pass) {
        $this->setPart('pass', $pass);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasPath() {
        return $this->hasPart('path');
    }
    
    
    /**
     *
     * @return \webignition\Url\Path\Path
     */
    public function getPath() {        
        return $this->getPart('path');
    }
    
    
    /**
     *
     * @param string $path 
     */
    public function setPath($path) {
        $this->setPart('path', $path);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasQuery() {
        return $this->hasPart('query');
    }
    
    
    /**
     *
     * @return \webignition\Url\Query\Query
     */
    public function getQuery() {
        $query = $this->getPart('query');
        if ($query instanceof Query\Query && !$query->hasConfiguration()) {
            $query->setConfiguration($this->getConfiguration());
        }
        
        return $query;
    }
    
    
    /**
     *
     * @param string $query 
     */
    public function setQuery($query) {
        $this->setPart('query', $query);
    }
    
    
    /**
     *
     * @return boolean
     */    
    public function hasFragment() {
        return $this->hasPart('fragment');
    }
    
    
    /**
     *
     * @return string
     */
    public function getFragment() {
        return $this->getPart('fragment');
    }
    
    
    /**
     *
     * @param string $fragment
     */
    public function setFragment($fragment) {
        $this->setPart('fragment', $fragment);
    }    

    
    /**
     *
     * @return string 
     */
    public function __toString() {        
        $url = $this->getRoot();
        
        $url .= $this->getPath();
        
        if ($this->hasQuery()) {
            $url .= '?' . $this->getQuery();
        }
        
        if ($this->hasFragment()) {
            $url .= '#' . $this->getFragment();
        }
        
        return $url;        
    }


    /**
     * Return a semantically marked-up string
     *
     * @return string
     */
    public function __toSemanticString() {
        return '';
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isRelative() {
        if ($this->hasScheme()) {
            return false;
        }
        
        if ($this->hasHost()) {
            return false;
        }
        
        return true;
    }
    
    
    /**
     *
     * @return boolean 
     */
    public function isProtocolRelative() {
        if ($this->hasScheme()) {
            return false;
        }
        
        return $this->hasHost();
    }
    

    /**
     *
     * @return boolean
     */
    public function isAbsolute() {
        if ($this->isRelative()) {
            return false;
        }
        
        return !$this->isProtocolRelative();
    }
    
    
    /**
     *
     * @param string $partName
     * @param string $value 
     */
    public function setPart($partName, $value) {
        if (!$this->hasPart($partName) && is_null($value)) {
            return;
        }

        if ($this->hasPart($partName)) {
            $this->replacePart($partName, $value);
        } else {
            $this->addPart($partName, $value);            
        }

        $this->reset();
    }
    
    
    /**
     * 
     * @param string $partName
     * @param string $value 
     */
    private function replacePart($partName, $value) {
        if ($partName == 'scheme' && is_null($value)) {
            $offsets = &$this->offsets();
            $this->originUrl = '//' . substr($this->originUrl, $offsets['host']);
            return;
        }
         
        if ($partName == 'query' && substr($value, 0, 1) == '?') {
            $value = substr($value, 1);
        }
        
        if ($partName == 'fragment' && substr($value, 0, 1) == '#') {
            $value = substr($value, 1);
        }

        $offsets = &$this->offsets();

        if ($partName == 'fragment' && is_null($value) && isset($offsets['fragment'])) {
            if ($this->getFragment() == '') {
                $this->originUrl = substr($this->originUrl, 0, $offsets['fragment']);
            } else {
                $this->originUrl = substr($this->originUrl, 0, $offsets['fragment'] - 1);
            }

            return;
        }
        
        $this->originUrl = substr_replace($this->originUrl, $value, $offsets[$partName], strlen($this->getPart($partName)));
    }
    
    
    private function addPart($partName, $value) {
        if ($partName == 'scheme') {
            return $this->addScheme($value);
        }
        
        if ($partName == 'user') {
            return $this->addUser($value);
        }
        
        if ($partName == 'host') {
            return $this->addHost($value);
        }        
        
        if ($partName == 'pass') {
            return $this->addPass($value);
        }
        
        if ($partName == 'query') {
            return $this->addQuery($value);
        }
        
        if ($partName == 'fragment') {
            return $this->addFragment($value);
        }
        
        if ($partName == 'path') {
            return $this->addPath($value);
        }

        if ($partName == 'port') {
            return $this->addPort($value);
        }
    }
    
    
    /**
     * Add a scheme to a URL that does not already have one
     * 
     * @param string $scheme
     * @return boolean 
     */
    private function addScheme($scheme) {
        if ($this->hasScheme()) {
            return false;
        }
        
        if (!$this->isProtocolRelative()) {
            $this->originUrl = '//' . $this->originUrl;
        }
        
        if (substr($this->originUrl, 0, 1) != ':') {
            $this->originUrl = ':' . $this->originUrl;
        }
        
        $this->originUrl = $scheme . $this->originUrl;
    }
    
    
    /**
     * Add a user to a URL that does not already have one
     * 
     * @param string $user
     * @return boolean 
     */
    private function addUser($user) {
        if ($this->hasUser()) {
            return false;
        }
        
        if (!is_string($user)) {
            $user = '';
        }
        
        $user = trim($user);
//        if ($user == '') {
//            return true;
//        }
        
        // A user cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }
        
        $nextPartName = $this->getNextPartName('user');
        $offsets = &$this->offsets();
        
        if ($nextPartName == 'host') {
            $preNewPart = substr($this->originUrl, 0, $offsets[$nextPartName]);            
            $postNewPart = substr($this->originUrl, $offsets[$nextPartName]);              
            
            return $this->originUrl = $preNewPart . $user . '@' . $postNewPart;
        }

        $preNewPart = substr($this->originUrl, 0, $offsets[$nextPartName] - 1);
        $postNewPart = substr($this->originUrl, $offsets[$nextPartName] - 1);

        return $this->originUrl = $preNewPart . $user . $postNewPart;
    }
    
    
    private function addPass($pass) {       
        if ($this->hasPass()) {
            return false;
        }       
        
        // A pass cannot be added to a URL that has no host; this results in
        // an invalid URL.
        if (!$this->hasHost()) {
            return false;
        }
        
        $offsets = &$this->offsets();
        
        if ($this->hasUser()) {
            $preNewPart = substr($this->originUrl, 0, $offsets['host'] - 1);
            $postNewPart = substr($this->originUrl, $offsets['host'] - 1);

            return $this->originUrl = $preNewPart . $pass . $postNewPart;
        }
        
        $preNewPart = substr($this->originUrl, 0, $offsets['host']);
        $postNewPart = substr($this->originUrl, $offsets['host']);

        return $this->originUrl = $preNewPart . ':' . $pass . '@' . $postNewPart;        
    }
    
    
    /**
     * Add a host to a URL that does not already have one
     * 
     * @param string $host
     * @return boolean 
     */
    private function addHost($host) {        
        if ($this->hasHost()) {
            return false;
        }
 
        if ($this->hasPath() && $this->getPath()->isRelative()) {
            $this->setPath('/' . $this->getPath());
        }
        
        return $this->originUrl = '//' . $host . $this->originUrl;        
    }
    
    
    /**
     * Add query to a URL that does not already have one
     * 
     * @param string $query
     * @return boolean 
     */
    private function addQuery($query) {
        if ($this->hasQuery()) {
            return false;
        }
        
        if (is_null($query)) {
            return true;
        }
        
        if (substr($query, 0, 1) != '?') {
            $query = '?' . $query;
        }
        
        if ($this->hasFragment()) {
            $offsets = &$this->offsets();
            $preNewPart = substr($this->originUrl, 0, $offsets['fragment'] - 1);
            $postNewPart = substr($this->originUrl, $offsets['fragment'] - 1);

            return $this->originUrl = $preNewPart . $query . $postNewPart;          
        }
        
        return $this->originUrl .= $query;
    }
    
    
    /**
     * Add a fragment to a URL that does not already have one
     * 
     * @param string $fragment
     * @return boolean 
     */
    public function addFragment($fragment) {
        if ($this->hasFragment()) {
            return false;
        }
        
        if (!is_string($fragment)) {
            $fragment = '';
        }
        
        $fragment = trim($fragment);
        
        if ($fragment == '') {
            return true;
        }

        if (substr($fragment, 0, 1) != '#') {
            $fragment = '#' . $fragment;
        }
        
        return $this->originUrl .= $fragment;
    }
    
    
    /**
     *  Add a path to a URL that does not already have one
     * 
     * @param string $path
     * @return boolean 
     */
    public function addPath($path) {       
        if ($this->hasPath()) {
            return false;
        }
        
        if (!$this->hasPart('query') && !$this->hasPart('fragment')) {
            return $this->originUrl = $this->originUrl . $path;
        }
        
        $nextPartName = $this->getNextPartName('path');
        $offsets = &$this->offsets();             
        
        $offset = $offsets[$nextPartName];
        
        if ($nextPartName == 'fragment' && $offset > 0) {
            $offset -= 1;
        }
        
        return $this->originUrl = substr($this->originUrl, 0, $offset) . $path . substr($this->originUrl, $offset);        
    }
    

    public function addPort($value)
    {
        if (!$value or $this->hasPort()) {
            return false;
        }

        $this->parts['port'] = $value;
        $host = $this->getHost();

        return $this->originUrl = str_replace($host, $host.':'.$value, $this->originUrl);
    }

    
    /**
     * Get the next url part after $partName that is present in this
     * url
     * 
     * @param string $partName
     * @return string
     */
    private function getNextPartName($partName) {        
        $hasFoundPart = false;        
        foreach ($this->availablePartNames as $availablePartName) {
            if ($partName == $availablePartName) {
                $hasFoundPart = true;
            }
            
            if ($hasFoundPart === false) {
                continue;
            }
            
            if ($availablePartName != $partName && $this->hasPart($availablePartName)) {
                return $availablePartName;
            }
        }
        
        return null;
    }
    
    
    private function &offsets() {        
        if (is_null($this->offsets)) {
            $this->offsets = array();
            
            $partNames = array();
            foreach ($this->availablePartNames as $availablePartName) {
                if ($this->hasPart($availablePartName)) {
                    $partNames[] = $availablePartName;
                }
            }

            if (count($partNames) == 1) {
                $this->offsets = array(
                    $partNames[0] => 0
                );
                
                return $this->offsets;              
            }

            $originUrlComparison = str_split(rawurldecode($this->originUrl));
            $index = 0;

            foreach ($partNames as $partName) {
                $currentPart = urldecode((string)$this->parts[$partName]);

                // Special case: empty user (i.e. user = '', not null or missing user)
                if ($partName == 'user' && $currentPart == '') {
                    if (array_slice($originUrlComparison, 0, 3) ==  array(':', '/', '/')) {
                        $this->offsets['user'] = $this->offsets['scheme'] + strlen(urldecode((string)$this->parts['scheme'])) + 3;
                        continue;
                    } elseif (array_slice($originUrlComparison, 0, 2) ==  array('/', '/')) {
                        $this->offsets['user'] = 2;
                        continue;
                    }
                }

                // Special case: empty password
                if ($partName == 'pass' && $currentPart == '') {
                    $this->offsets['pass'] = $this->offsets['user'] + strlen($this->parts['user']) + 1;
                    continue;
                }

                $currentPartMatch = '';
                $currentPartFirstCharacter = substr($currentPart, 0, 1);

                while ($currentPartMatch != $currentPart) {
                    if(!isset($originUrlComparison[0])){
                        break;
                    }
                    
                    $currentCharacter = $originUrlComparison[0];

                    $nextCharacter = array_shift($originUrlComparison);
                    $index++;

                    if ($currentPartMatch == '' && $nextCharacter != $currentPartFirstCharacter) {
                        continue;
                    }

                    $currentPartMatch .= $currentCharacter;
                }

                $offset = $index - strlen($currentPartMatch);
                $this->offsets[$partName] = $offset;
            }
        }
        
        return $this->offsets;
    }
    
    
    protected function reset() {
        $this->parser = null;
        $this->parts = $this->getParser()->getParts();
        $this->offsets = null;
    }
    
    
    /**
     *
     * @return boolean
     */
    public function hasCredentials() {
        return $this->hasUser() || $this->hasPass();
    }
    
    
    /**
     *
     * @return string 
     */
    private function getCredentials() {
        $credentials = '';
        
        if ($this->hasUser()) {
            $credentials .= $this->getUser();
        }

        if ($this->hasPass()) {
            $credentials .= ':';
            $credentials .= $this->getPass();
        }
        
        return $credentials;
    } 
    
    
    /**
     *
     * @param string $partName
     * @return mixed
     */
    protected function getPart($partName) {        
        $parts = &$this->parts();
        
        return (isset($parts[$partName])) ? $parts[$partName] : null;
    }
    
    
    /**
     *
     * @param string $partName
     * @return boolean
     */
    protected function hasPart($partName) {
        if (is_null($this->getPart($partName))) {
            return false;
        }
        
        return isset($this->parts[$partName]);
    }

    
    /**
     *
     * @return \webignition\Url\Parser
     */
    public function getParser() {
        if (is_null($this->parser)) {
            $this->parser = new Parser($this->prepareOriginUrl());
        }
        
        return $this->parser;
    }
    
    
    /**
     * 
     * @return string
     */
    private function prepareOriginUrl() {
        $preparedOriginUrl = $this->originUrl;
        
        // Unencoded leading or trailing whitespace is not allowed
        $preparedOriginUrl = trim($preparedOriginUrl);
        
        // Whitespace that is not a regular space character is not allowed
        // and should be removed.
        // 
        // Not clearly spec'd anywhere but is the default behaviour of Chrome
        // and FireFox
        $preparedOriginUrl = str_replace(array("\t", "\r", "\n"), '', $preparedOriginUrl);
        
        return $preparedOriginUrl;
    }
    
    
    /**
     * 
     * @return \webignition\Url\Configuration
     */
    public function getConfiguration() {
        if (is_null($this->configuration)) {
            $this->configuration = new Configuration();
        }
        
        return $this->configuration;
    }    
    
}
