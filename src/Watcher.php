<?php

namespace HackerESQ\Watcher;

/**
 * INSTALL:
 * 
 * 1. Create custom FormRequest and "use" this trait in your custom FormRequest
 * 2. Use the default WatcherRequest if you don't need your own FormRequest
 * 3. Add your watched triggers with $request->setWatcher()
 * 
 * USAGE:
 * 
 * $request->setWatcher([
 *      'invoice_start_num_changed' => [
 *          'action' => fn($context) => DB::statement("ALTER TABLE `invoices` AUTO_INCREMENT = ".(int)$context->request->invoice_start_num),
 *          'removeKey' => true,
 *      ],
 *  ]);
 * 
 */ 

trait Watcher
{
    private array $triggers = [];
    private array $keys_to_remove = [];

    /**
     * Sets the triggers for the watcher.
     *
     * @param array $triggers The keys to watch and the actions to perform if present in request
     * @return void
     */
    public function setWatcher($triggers) {
        $this->triggers = $triggers;

        $this->watch();
    }

    /**
     * Watches for specific keys and performs the defined action.
     *
     * @return void
     */
    private function watch() {
        // loop through triggers
        foreach($this->triggers as $trigger => $meta) {
            // does this trigger exist in the request?
            if ( $this->has($trigger) && (isset($meta['allowEmpty'] && $meta['allowEmpty']) || !empty($this->{$trigger}) )) {
                // run action and provide context to function as an object
                if (isset($meta['action'])) $meta['action']((object)[
                    'request' => $this,
                    'trigger' => $trigger
                ]);

                // should we remove this key?
                if (isset($meta['removeKey']) && $meta['removeKey']) array_push($this->keys_to_remove,$trigger);
            }
        }
        // are there any keys to remove? if so, do it...
        if ($this->keys_to_remove) $this->replace($this->except($this->keys_to_remove));
    }

}
