This module is a realy simple class generator based on already existing
Database Table. It is used in Xoops administrator module.
The class generated provides an access mechanism to the data source.
Its purpose is to generate a file corresponding to a DB table in order to
manipulate objects.
Each generated file contains 2 classes :
- The first one, extend XoopsObject, is intended to manipulate simple
object.
- The second, extend XoopsObjectHandler, is intended to create, modify and
retrieve objects according to selected criteria.
The class model used in this module is similar to the commonly used xoops
kernel model object.
Class generator is simply based on a smarty template that can be customized.
