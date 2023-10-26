<?php declare(strict_types=1);

    namespace STDW\Flash;

    use STDW\Contract\FlashInterface;
    use STDW\Contract\SessionInterface;
    use STDW\Support\Arr;


    class Flash implements FlashInterface
    {
        protected SessionInterface $session;

        protected string $storage_id = '__FLASH__';


        public function __construct(SessionInterface $session)
        {
            $this->session = $session;
        }


        public function get(string $key): mixed
        {
            $messages = $this->session->get($this->storage_id);

            if ( ! array_key_exists($key, $messages)) {
                return null;
            }

            $item = Arr::grab($key, $messages);

            $this->session->set($this->storage_id, $messages);

            return $item['message'];
        }

        public function set(string $key, mixed $message, int $age = 1): void
        {
            $messages = $this->session->get($this->storage_id);

            $messages[$key] = [
                'message' => $message,
                'age' => $age,
            ];

            $this->session->set($this->storage_id, $messages);
        }

        public function exists(string $_key): bool
        {
            $messages = $this->session->get($this->storage_id);

            return isset($messages[$_key]);
        }

        public function clear(): void
        {
            $messages = $this->session->get($this->storage_id, []);

            foreach ($messages as $key => &$item) {
                $item['age']--;

                if ($item['age'] < 0) {
                    unset($messages[$key]);
                }
            }

            $this->session->set($this->storage_id, $messages);
        }
    }