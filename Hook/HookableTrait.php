<?php
namespace Asgard\Hook;

/**
 * Extend "Hookable" to make hookable instances.
 * @author Michel Hognerud <michel@hognerud.net>
*/
trait HookableTrait {
	/**
	 * HookManager dependency.
	 * @var HookManagerInterface
	 */
	protected $HookManager;

	/**
	 * Check if has a hook.
	 * @param string $name
	 * @return boolean
	*/
	public function hasHook($name) {
		if(!$this->getHookManager())
			return false;
		return $this->getHookManager()->has($name);
	}

	/**
	 * Trigger a hook.
	 * @param string    $name
	 * @param array     $args
	 * @param callable  $cb
	 * @param Chain $chain
	 * @return mixed
	*/
	public function trigger($name, array $args=[], $cb=null, &$chain=null) {
		if(!$this->getHookManager())
			return;
		return $this->getHookManager()->trigger($name, $args, $cb, $chain);
	}

	/**
	 * Set a hook.
	 * @param string   $hookName
	 * @param callable $cb
	 * @return mixed
	*/
	public function hook($hookName, $cb) {
		$args = [$hookName, $cb];
		return call_user_func_array([$this->getHookManager(), 'hook'], $args);
	}

	/**
	 * Set a "before" hook.
	 * @param string   $hookName
	 * @param Callable $cb
	 * @return mixed
	*/
	public function hookBefore($hookName, $cb) {
		$args = [$hookName, $cb];
		return call_user_func_array([$this->getHookManager(), 'hookBefore'], $args);
	}

	/**
	 * Set an "after" hook.
	 *
	 * @param string   $hookName
	 * @param callable $cb
	 * @return mixed
	*/
	public function hookAfter($hookName, $cb) {
		$args = [$hookName, $cb];
		return call_user_func_array([$this->getHookManager(), 'hookAfter'], $args);
	}

	/**
	 * Get the hooks manager.
	 * @return HookManagerInterface
	*/
	public function getHookManager() {
		if(!$this->HookManager)
			$this->HookManager = new \Asgard\Hook\HookManager;
		return $this->HookManager;
	}

	/**
	 * Set the hooks manager.
	 * @param HookManagerInterface $HookManager
	*/
	public function setHookManager(HookManagerInterface $HookManager) {
		$this->HookManager = $HookManager;
	}
}
