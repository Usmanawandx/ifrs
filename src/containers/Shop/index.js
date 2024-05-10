import React, { useState } from 'react';
import {
  ScrollView, StyleSheet, View,
} from 'react-native';
import PropTypes from 'prop-types';
import {
  Container,
  NavBar,
  ProductList,
  Text,
  HorizontalProductList,
  IconButton,
} from 'components';
import { getShopById } from 'mocks/shops';
import { getScreenHeight } from 'utils/size';
import Colors from 'themes/colors';
import { scale } from 'react-native-size-matters';
import { getNProducts } from 'mocks/products';
import Header from './Header';
import Stats from './Stats';

const styles = StyleSheet.create({
  navbar: {
    position: 'absolute',
    top: 0,
    left: 0,
    right: 0,
  },
  featured: {
    marginVertical: scale(14),
    backgroundColor: Colors.white,
    paddingVertical: scale(14),
  },
  products: {
    backgroundColor: Colors.white,
    paddingVertical: scale(14),
  },
  title: {
    marginHorizontal: scale(14),
  },
  control: {
    flexDirection: 'row',
  },
  icon: {
    paddingLeft: scale(14),
  },
});

const featured = getNProducts(6);
const latest = getNProducts(8);

const Shop = ({ navigation, route }) => {
  const { id } = route.params;
  const shop = getShopById(id);
  const [variant, setVariant] = useState('ghost');
  const onScroll = (y) => {
    setVariant(y > getScreenHeight() / 4 ? 'gradient' : 'ghost');
  };

  return (
    <Container>
      <ScrollView
        onScroll={({ nativeEvent: { contentOffset: { y } } }) => onScroll(y)}
        scrollEventThrottle={16}
      >
        <Header shop={shop} />

        <Stats shop={shop} />
        <View style={styles.featured}>
          <Text weight="medium" font="p1" style={styles.title}>Featured Products</Text>
          <HorizontalProductList
            products={featured}
            navigation={navigation}
          />
        </View>
        <View style={styles.products}>
          <Text weight="medium" font="p1" style={styles.title}>Latest Products</Text>
          <ProductList
            products={latest}
            navigation={navigation}
          />
        </View>
      </ScrollView>
      <NavBar
        onLeftIconPress={() => navigation.goBack()}
        containerStyle={styles.navbar}
        title={variant !== 'ghost' ? shop.name : null}
        variant={variant}
        renderRightComponent={() => (
          <View style={styles.control}>
            <IconButton
              icon="heart"
              color="white"
              style={styles.icon}
            />
            <IconButton
              icon="more-horizontal"
              color="white"
              style={styles.icon}
            />
          </View>
        )}
      />
    </Container>
  );
};

Shop.propTypes = {
  navigation: PropTypes.object.isRequired,
  route: PropTypes.object.isRequired,
};

export default Shop;
