/**
 * PitonCMS (https://github.com/PitonCMS)
 *
 * @link      https://github.com/PitonCMS/Piton
 * @copyright Copyright 2018 Wolfgang Moritz
 * @license   AGPL-3.0-or-later with Theme Exception. See LICENSE file for details.. See LICENSE file for details.
 */

/**
 * Manage Content JS
 */

import "./modules/main.js";
import { pitonConfig } from './modules/config.js';
import { setQueryRequestPath } from "./modules/filter.js";

setQueryRequestPath(pitonConfig.routes.adminPageGet);