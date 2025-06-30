.. This is a comment. Note how any initial comments are moved by
   transforms to after the document title, subtitle, and docinfo.

.. demo.rst from: http://docutils.sourceforge.net/docs/user/rst/demo.txt

.. |EXAMPLE| image:: static/yi_jing_01_chien.jpg
   :width: 1em

**********************
Datasets
**********************

.. contents:: Table of Contents
Overview
==================

Data Sources are added via the Data Source menu.

By default, Jasper Report Publisher includes support for the following:

PostgreSQL (JNDI)
Oracle (JDBC and JNDI)
MySQL (JNDI)
MSSQL (JNDI)

Add Dataset
================

To manually add a dataset, complete the required fields

1

.. image:: ../../_static/metadata-add-1.png

2

.. image:: ../../_static/metadata-add-2.png


3

.. image:: ../../_static/metadata-add-3.png

4

.. image:: ../../_static/metadata-add-4.png

5

.. image:: ../../_static/metadata-add-5.png

6

.. image:: ../../_static/metadata-add-6.png

7

.. image:: ../../_static/metadata-add-7.png

8

.. image:: ../../_static/metadata-add-8.png


9

.. image:: ../../_static/metadata-add-9.png

10

.. image:: ../../_static/metadata-add-10.png

11

.. image:: ../../_static/metadata-add-11.png

12

.. image:: ../../_static/metadata-add-12.png

13

.. image:: ../../_static/metadata-add-13.png

14

.. image:: ../../_static/metadata-add-14.png

15

.. image:: ../../_static/metadata-add-15.png

Select Type (JNDI or JDBC)

Data Source Name (this should match name used in Jasper Report Adapter)

URL: The JDBC URL (e.g. jdbc:postgresql://localhost:5432/beedatabase)

Username

Password

You can add as many Data Sources as you wish to:

.. image:: ../../_static/data-source-2.png

Restart Tomcat
================

You must restart Tomcat after adding or editing Data Sources in order to pick up the new configuration

.. image:: ../../_static/tomcat-restart.png









