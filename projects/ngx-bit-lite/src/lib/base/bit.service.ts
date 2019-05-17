import {Injectable} from '@angular/core';
import {ConfigService} from './config.service';

@Injectable()
export class BitService {
  /**
   * Static Path
   */
  static: string;

  /**
   * Upload Path
   */
  uploads: string;

  constructor(private config: ConfigService) {
    this.static = config.staticUrl;
    this.uploads = (config.uploadsUrl) ? config.uploadsUrl : config.originUrl + '/' + config.uploadsPath;
  }

}
