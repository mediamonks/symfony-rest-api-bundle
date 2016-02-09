<?php

namespace MediaMonks\RestApiBundle\Response;

class Error
{
    const CODE_GENERAL = 'error.%s';
    const CODE_HTTP = 'error.http.%s';
    const CODE_FORM_VALIDATION = 'error.form.validation';
    const CODE_REST_API_BUNDLE = 'error.rest_api_bundle';

    const MESSAGE_FORM_VALIDATION = 'Not all fields are filled incorrectly.';
}
