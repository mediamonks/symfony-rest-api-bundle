MediaMonksRestApiBundle Configuration Reference
===============================================

All available configuration options are listed below with their default values.

.. code-block:: yaml

    mediamonks_rest_api:
        debug: %kernel.debug%
        post_message_origin:
        request_matcher:
            whitelist: [~^/api/$~,  ~^/api~]
            blacklist: [~^/api/doc~]
        serializer: json
        response_model: mediamonks_rest_api.response_model
