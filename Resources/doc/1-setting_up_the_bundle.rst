Step 1: Setting up the bundle
=============================

A) Download the Bundle
----------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

.. code-block:: bash

    $ composer require mediamonks/rest-api-bundle "~1.0@dev"

This command requires you to have Composer installed globally, as explained
in the `installation chapter`_ of the Composer documentation.

B) Enable the Bundle
--------------------

Then, enable the bundle by adding the following line in the ``app/AppKernel.php``
file of your project:

.. code-block:: php

    // app/AppKernel.php
    class AppKernel extends Kernel
    {
        public function registerBundles()
        {
            $bundles = [
                // ...
                new MediaMonks\RestApiBundle\MediaMonksRestApiBundle(),
            ];

            // ...
        }
    }

C) Enable JMS Serializer
------------------------

This bundle needs `JMSSerializerBundle`_ to work correctly so make that's setup as well.

.. _`installation chapter`: https://getcomposer.org/doc/00-intro.md
.. _`JMSSerializerBundle`: https://github.com/schmittjoh/JMSSerializerBundle
