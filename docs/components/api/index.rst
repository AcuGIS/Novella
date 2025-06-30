.. This is a comment. Note how any initial comments are moved by
   transforms to after the document title, subtitle, and docinfo.

.. demo.rst from: http://docutils.sourceforge.net/docs/user/rst/demo.txt

.. |EXAMPLE| image:: static/yi_jing_01_chien.jpg
   :width: 1em

**********************
API
**********************

.. contents:: Table of Contents

Dataset Operations
=================

Available Endpoints:

GET
  /api/datasets
    - List all datasets
GET
  /api/datasets/{id}
    - Get specific dataset
POST
  /api/datasets
    - Create new dataset
PUT
  /api/datasets/{id}
    - Update dataset
DELETE
  /api/datasets/{id}
    - Delete dataset


Export Operations
=================
GET
  /api/datasets/{id}/xml
    - Export dataset to XML
GET
  /api/datasets/{id}/json
    - Export dataset to JSON
 
Authorization: Bearer your-token-here



