## Error Handling ##

An `Stackla\Exception\ApiException` is thrown when there are errors.

```
try {
    // do something with SDK that could cause errors
} catch (\Stackla\Exception\ApiException $exception) {
    $httpStatusCode = $exception->getHttpStatusCode();
    $httpResponseBody = $exception->getHttpResponseBody();

    // an array of Stackla\Exception\ApiError instances
    $errors = $exception->getErrors();
    foreach ($errors as $error) {
        $message = $error->getMessage(); // human readable message
        $errorCode = $error->getErrorCode(); // unique error code
        $messageId = $error->getMessageId(); // unique error message ID
    }

    // some helper functions
    // check if a certain error exists by error code
    if ($exception->containsErrorByErrorCode(1070409)) {
        // tag name conflict error
        echo 'Tag name must be unique';
    }

    // check if a certain error exists by message ID
    if ($exception->containsErrorByMessageId('term:not_found')) {
        // the given term cannot be found
        echo 'Term cannot be found';
    }
}
```