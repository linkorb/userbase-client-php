UPGRADE FROM 1.9 to 2.0
=======================

- The User class no longer implements Symfony's `AdvancedUserInterface` which
  it has been removed from Symfony 5.  Use of the methods of this interface
  should be replaced by performing User and Account checks in UserCheckers.

  A temporary replacement for the removed interface is
  `UserBase\Client\Model\LegacyAdvancedUserInterface`, but use this only as a
  last resort and as a temporary fix.
