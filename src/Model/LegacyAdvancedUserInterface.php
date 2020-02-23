<?php

namespace UserBase\Client\Model;

/**
 * For backwards compatability only - use UserCheckers instead.
 *
 * This iface replaces Symfony\Component\Security\Core\User\AdvancedUserInterface
 * which was removed from Symfony 5 because the User class is not the right place
 * to implement account/user checks that influence whether or not a user is
 * allowed to authenticate.  For more info about why it was removed see:
 * https://github.com/symfony/symfony/issues/23292
 *
 * For info about UserCheckers see:
 * https://symfony.com/doc/current/security/user_checkers.html
 */
interface LegacyAdvancedUserInterface extends UserInterface
{
    /**
     * Check whether the user's account has expired.
     *
     * @return bool true if the user's account is non expired, false otherwise
     */
    public function isAccountNonExpired();

    /**
     * Check whether the user is locked.
     *
     * @return bool true if the user is not locked, false otherwise
     */
    public function isAccountNonLocked();

    /**
     * Check whether the user's credentials have expired.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     */
    public function isCredentialsNonExpired();

    /**
     * Check whether the user is enabled.
     *
     * @return bool true if the user is enabled, false otherwise
     */
    public function isEnabled();
}
