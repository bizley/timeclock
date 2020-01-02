<?php

declare(strict_types=1);

namespace app\base;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\di\Instance;
use yii\web\Session;

/**
 * Class Alert
 * @package app\base
 */
class Alert extends Component
{
    /**
     * @var string|array|Session
     */
    public $handler = 'session';

    /**
     * Ensures handler is Session object.
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();
        $this->handler = Instance::ensure($this->handler, Session::class);
    }

    /**
     * Adds flash message of 'danger' type.
     * @param string $message
     * @param bool $override whether to set flash message instead of adding it
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function danger(string $message, bool $override = false, bool $removeAfterAccess = true): void
    {
        if ($override) {
            $this->set('danger', $message, $removeAfterAccess);
        } else {
            $this->add('danger', $message, $removeAfterAccess);
        }
    }

    /**
     * Adds flash message of 'success' type.
     * @param string $message
     * @param bool $override whether to set flash message instead of adding it
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function success(string $message, bool $override = false, bool $removeAfterAccess = true): void
    {
        if ($override) {
            $this->set('success', $message, $removeAfterAccess);
        } else {
            $this->add('success', $message, $removeAfterAccess);
        }
    }

    /**
     * Adds flash message of 'info' type.
     * @param string $message
     * @param bool $override whether to set flash message instead of adding it
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function info(string $message, bool $override = false, bool $removeAfterAccess = true): void
    {
        if ($override) {
            $this->set('info', $message, $removeAfterAccess);
        } else {
            $this->add('info', $message, $removeAfterAccess);
        }
    }

    /**
     * Adds flash message of 'warning' type.
     * @param string $message
     * @param bool $override whether to set flash message instead of adding it
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function warning(string $message, bool $override = false, bool $removeAfterAccess = true): void
    {
        if ($override) {
            $this->set('warning', $message, $removeAfterAccess);
        } else {
            $this->add('warning', $message, $removeAfterAccess);
        }
    }

    /**
     * Alias for danger().
     * @see danger()
     * @param string $message
     * @param bool $override whether to set flash message instead of adding it
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function error(string $message, bool $override = false, bool $removeAfterAccess = true): void
    {
        $this->danger($message, $override, $removeAfterAccess);
    }

    /**
     * Returns a flash message.
     * @param string $key the key identifying the flash message
     * @param mixed $defaultValue value to be returned if the flash message does not exist.
     * @param bool $delete whether to delete this flash message right after this method is called.
     * If false, the flash message will be automatically deleted in the next request.
     * @return mixed the flash message or an array of messages if addFlash was used
     */
    public function get(string $key, $defaultValue = null, bool $delete = false)
    {
        return $this->handler->getFlash($key, $defaultValue, $delete);
    }

    /**
     * Adds a flash message.
     * If there are existing flash messages with the same key, the new one will be appended to the existing message array.
     * @param string $key the key identifying the flash message.
     * @param mixed $value flash message
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function add(string $key, $value = true, bool $removeAfterAccess = true): void
    {
        $this->handler->addFlash($key, $value, $removeAfterAccess);
    }

    /**
     * Returns all flash messages.
     * @param bool $delete whether to delete the flash messages right after this method is called.
     * If false, the flash messages will be automatically deleted in the next request.
     * @return array flash messages (key => message or key => [message1, message2]).
     */
    public function getAll(bool $delete = false): array
    {
        return $this->handler->getAllFlashes($delete);
    }

    /**
     * Sets a flash message.
     * A flash message will be automatically deleted after it is accessed in a request and the deletion will happen
     * in the next request.
     * If there is already an existing flash message with the same key, it will be overwritten by the new one.
     * @param string $key the key identifying the flash message. Note that flash messages
     * and normal session variables share the same name space. If you have a normal
     * session variable using the same name, its value will be overwritten by this method.
     * @param mixed $value flash message
     * @param bool $removeAfterAccess whether the flash message should be automatically removed only if
     * it is accessed. If false, the flash message will be automatically removed after the next request,
     * regardless if it is accessed or not. If true (default value), the flash message will remain until after
     * it is accessed.
     */
    public function set(string $key, $value = true, bool $removeAfterAccess = true): void
    {
        $this->handler->setFlash($key, $value, $removeAfterAccess);
    }

    /**
     * Removes a flash message.
     * @param string $key the key identifying the flash message. Note that flash messages
     * and normal session variables share the same name space.  If you have a normal
     * session variable using the same name, it will be removed by this method.
     * @return mixed the removed flash message. Null if the flash message does not exist.
     */
    public function remove(string $key)
    {
        return $this->handler->removeFlash($key);
    }

    /**
     * Removes all flash messages.
     * Note that flash messages and normal session variables share the same name space.
     * If you have a normal session variable using the same name, it will be removed
     * by this method.
     */
    public function removeAll(): void
    {
        $this->handler->removeAllFlashes();
    }

    /**
     * Returns a value indicating whether there are flash messages associated with the specified key.
     * @param string $key key identifying the flash message type
     * @return bool whether any flash messages exist under specified key
     */
    public function has(string $key): bool
    {
        return $this->handler->hasFlash($key);
    }
}
