import {Injectable} from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {switchMap} from 'rxjs/operators';
import {Observable} from 'rxjs';
import {ConfigService} from './config.service';

@Injectable()
export class HttpService {
  constructor(private http: HttpClient,
              private config: ConfigService) {
  }

  /**
   * HttpClient
   */
  req(url: string, body: any = {}, method = 'post'): Observable<any> {
    const httpClient = this.http.request(method, this.config.originUrl + this.config.namespace + '/' + url, {
      body,
      withCredentials: this.config.withCredentials
    });
    return !this.config.httpInterceptor ? httpClient : httpClient.pipe(
      switchMap(res => this.config.interceptor(res))
    );
  }
}
