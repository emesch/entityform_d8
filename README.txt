Entityform for D7 was created by Ted Bowman.

This experimental (and very incomplete!) D8 port copyright Morgan Esch, 2015

For latest documentation visit: http://drupal.org/node/1432894
Introduction
------------
This module allows creating front-end forms using Drupal's Field systems.

For more information on adding fields see the Field UI documentation here: http://drupal.org/documentation/modules/field-ui




Installation
-------------
Once you activate the module it sets up an entity administration interface under
Administration > Content > Entityform Types


Usage
---------------
1. Enable the module
2. Goto admin/structure/eform/types
3. Click "Add an Entityform Type"
4. Fill out basic form information. Under Access Settings make sure at least 1 role can submit the form
5. Click "Save Entityform Type"
6. Click manage fields and add fields the same way you would for a node content type.

Module Integration
---------------------
The aim of this module is create a form creation method that leverages that power of entities and fields but is
separate from from the content (i.e. node) system.
