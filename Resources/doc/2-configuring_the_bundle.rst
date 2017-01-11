Step 2: Configuring the bundle
==============================

Debug Mode
----------

When debug mode is enabled a stack trace will be outputted when an exception is detected.
Debug mode is automatically enabled when your app is in debug mode.

You can enable or disable it manually by adding it to your configuration:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        debug: true/false

Request Matching
----------------

The bundle uses regexes to check if a request should be handled by this bundle. By default the bundle matches on /api*
with the exception of /api/doc* so you can put your documentation there.

You can override these regexes by configuring your own:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        request_matcher:
            whitelist: [~^/api/$~,  ~^/api~]
            blacklist: [~^/api/doc~]

Serializer
----------

You can configure the serializer which is used.

By default a json serializer is configured.

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        serializer: json

Post Message Origin
-------------------

Because of security reasons the default post message origin is empty by default.

You can set it by adding it to your configuration:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        post_message_origin: http://www.mediamonks.com/

Wrap Response Data
------------------

You can disable response wrapping if needed

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        wrap_response_data: false