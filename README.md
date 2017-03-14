# jira-rest-api
Jira API connector in PHP

https://docs.atlassian.com/jira/REST/cloud/

I created this package because the older package I was using fell
out of compatiblity with Atlassian's API.  Specifically the authentication was 
no longer supported and I needed cookie based authentication.  The Atlassian
documentation is here:

https://developer.atlassian.com/jiradev/jira-apis/jira-rest-apis/jira-rest-api-tutorials/jira-rest-api-example-cookie-based-authentication

### Usage ###

Creating a Jira Client

```php
$httpClient = new \GuzzleHttp\Client();
$jira = new \Bitsbybit\Jira\Session\Session($httpClient, "example.domain.com", [
    'ssl' => true
]);
```
OR to use a default GuzzleHttp Client
```php
$jira = \Bitsbybit\Jira\Session\Session::create("example.domain.com", [
    'ssl' => true
]);
```

Login
```php
try{
    $jira->login("username", "password");
}catch(\Bitsbybit\Jira\Session\Exception $e){
    //Login failed 
}
```
Perhaps you already logged in and have a session Id already:
```php
$jira = \Bitsbybit\Jira\Session\Session::create("example.domain.com", [
    'ssl' => true,
    'sessionName' => 'JSESSIONID',
    'sessionId' => '6E3487971234567896704A9EB4AE501F'
]);
```