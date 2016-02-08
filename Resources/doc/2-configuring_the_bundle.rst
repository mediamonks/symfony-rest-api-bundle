Step 2: Configuring the bundle
==============================

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

Output Formats
--------------

You can configure which output formats are available.

You can enable XML by adding it to your configuration:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        output_formats: [json, xml]

Post Message Origin
-------------------

Because of security reasons the default post message origin is empty by default.

You can set it by adding it to your configuration:

.. code-block:: yaml

    # app/config/config.yml
    mediamonks_rest_api:
        post_message_origin: http://www.mediamonks.com/