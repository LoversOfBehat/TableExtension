<?php

declare(strict_types = 1);

use Behat\Behat\Context\Context;
use LoversOfBehat\TableExtension\Hook\Scope\AfterTableFetchScope;

/**
 * Contains step definitions and hook implementations for testing TableExtension.
 */
class FeatureContext implements Context
{

  /**
   * Example implementation of the table hook.
   *
   * Strips elements from the table that are only intended to be seen when using
   * a screen reader.
   *
   * @AfterTableFetch
   */
  public static function stripScreenReaderElements(AfterTableFetchScope $scope) {
      $html_manipulator = new HtmlManipulator($scope->getHtml());
      $scope->setHtml($html_manipulator->removeElements('.visually-hidden')->html());
  }
}
