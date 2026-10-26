<?php

namespace Dwoo\Tests
{

    use Dwoo\Compiler;
    use Dwoo\Core;
    use PHPUnit\Framework\Constraint\Constraint;
    use PHPUnit\Framework\TestCase;

    /**
     * Class BaseTests
     *
     * @package Dwoo\Tests
     */
    abstract class BaseTests extends TestCase
    {

        protected $compileDir;
        protected $cacheDir;
        protected $compiler;
        protected $dwoo;

        /**
         * Set up a fresh compiler/dwoo instance for every test.
         */
        protected function setUp(): void
        {
            $this->compileDir = __DIR__ . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'cache';
            $this->cacheDir   = __DIR__ . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . 'compiled';

            // extend this class and override this in your setUp() to test a modded compiler
            $this->compiler = new Compiler();
            $this->dwoo     = new Core($this->compileDir, $this->cacheDir);
        }

        /**
         * Replacement for the removed TestCase::assertThat().
         *
         * @param mixed      $value
         * @param Constraint $constraint
         * @param string     $message
         */
        protected function assertConstraint($value, Constraint $constraint, string $message = ''): void
        {
            $this->assertTrue($constraint->evaluate($value, $message, true), $message);
        }

        /**
         * @param string $path
         * @param bool   $emptyOnly
         */
        protected function clearDir($path, $emptyOnly = false)
        {
            if (is_dir($path)) {
                foreach (glob($path . '/*') as $f) {
                    $this->clearDir($f);
                }
                if (!$emptyOnly) {
                    rmdir($path);
                }
            } elseif (is_file($path)) {
                unlink($path);
            }
        }
    }

    // Evaluates two strings and ignores differences in line endings (\r\n == \n == \r)
    class DwooConstraintStringEquals extends Constraint
    {
        protected $target;
        protected $other;

        public function __construct($target)
        {
            $this->target = preg_replace('#(\r\n|\r)#', "\n", $target);
        }

        protected function matches($other): bool
        {
            $this->other = preg_replace('#(\r\n|\r)#', "\n", $other);

            return $this->target == $this->other;
        }

        public function toString(): string
        {
            return 'equals expected value.' . PHP_EOL . 'Expected:' . PHP_EOL . $this->target . PHP_EOL . 'Received:' . PHP_EOL . $this->other . PHP_EOL;
        }
    }

    class DwooConstraintPathEquals extends Constraint
    {
        protected $target;
        protected $other;

        public function __construct($target)
        {
            $this->target = preg_replace('#([\\\\/]{1,2})#', '/', $target);
        }

        protected function matches($other): bool
        {
            $this->other = preg_replace('#([\\\\/]{1,2})#', '/', $other);

            return $this->target == $this->other;
        }

        public function toString(): string
        {
            return 'equals expected value.' . PHP_EOL . 'Expected:' . PHP_EOL . $this->target . PHP_EOL . 'Received:' . PHP_EOL . $this->other . PHP_EOL;
        }
    }
}
