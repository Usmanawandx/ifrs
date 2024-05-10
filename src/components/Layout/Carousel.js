import React from 'react';
import { StyleSheet, Image } from 'react-native';
import Swiper from 'react-native-swiper';
import { verticalScale } from 'react-native-size-matters';
import Colors from 'themes/colors';
import PropTypes from 'prop-types';

const styles = StyleSheet.create({
  wrapper: {
    height: verticalScale(150),
  },
  slide: {
    flex: 1,
    width: '100%',
    height: 100,
  },
});

const Carousel = ({ style, images, ...otherProps }) => (
  <Swiper
    style={StyleSheet.flatten([styles.wrapper, style])}
    autoplay
    activeDotColor={Colors.primary}
    dotColor={Colors.gray50}
    {...otherProps}
  >
    {images.map((image) => (
      <Image
        key={image}
        source={{uri:image}}
        resizeMode="cover"
        style={styles.slide}
      />
    ))}
  </Swiper>
);

Carousel.propTypes = {
  images: PropTypes.array,
  style: PropTypes.any,
};

Carousel.defaultProps = {
  style: null,
};

export default Carousel;
